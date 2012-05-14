<?php
class ModelModuleDeadcowSEO extends Model {
    public function generateCategories($template, $source_langcode) {
        $categories = $this->getCategories();
        $slugs = array();
        foreach ($categories as $category) {
            $tags = array('[category_name]' => $category['name']);
            $slug = $uniqueSlug = $this->makeSlugs(strtr($template, $tags), 0, true, $source_langcode);
            $index = 1;
            while (in_array($uniqueSlug, $slugs)) {
                $uniqueSlug = $slug . '-' . $index++;
            }
            $slugs[] = $uniqueSlug;
            $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category['category_id'] . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category['category_id'] . "', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
    }

    public function generateProducts($template, $source_langcode) {
        $products = $this->getProducts();
        $slugs = array();
        foreach ($products as $product) {
            $tags = array('[product_name]' => $product['name'],
                          '[model_name]' => $product['model'],
                          '[manufacturer_name]' => $product['manufacturer_name']
            );
            $slug = $uniqueSlug = $this->makeSlugs(strtr($template, $tags), 0, true, $source_langcode);
            $index = 1;
            while (in_array($uniqueSlug, $slugs)) {
                $uniqueSlug = $slug . '-' . $index++;
            }
            $slugs[] = $uniqueSlug;
            $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product['product_id'] . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product['product_id'] . "', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
    }

    public function generateManufacturers($template, $source_langcode) {
        $manufacturers = $this->getManufacturers();
        $slugs = array();
        foreach ($manufacturers as $manufacturer) {
            $tags = array('[manufacturer_name]' => $manufacturer['name']);
            $slug = $uniqueSlug = $this->makeSlugs(strtr($template, $tags), 0, true, $source_langcode);
            $index = 1;
            while (in_array($uniqueSlug, $slugs)) {
                $uniqueSlug = $slug . '-' . $index++;
            }
            $slugs[] = $uniqueSlug;
            $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer['manufacturer_id'] . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'manufacturer_id=" . (int)$manufacturer['manufacturer_id'] . "', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
    }

    public function generateMetaKeywords($template, $yahooID = null, $source_langcode) {
        $products = $this->getProductsForMetaKeywords();
        $slugs = array();
        foreach ($products as $product) {
            $finalCategories = array();
            $categories = $this->getProductCategories($product['product_id'], $product['language_id']);
            foreach ($categories as $category) {
                $finalCategories[] = $category['name'];
            }
            $tags = array('[product_name]' => $product['name'],
                          '[model_name]' => $product['model'],
                          '[manufacturer_name]' => $product['manufacturer_name'],
                          '[categories_names]' => implode(',', $finalCategories)

            );
            $finalKeywords = array();
            $keywords = explode(',', strtr($template, $tags));
            if ($yahooID != null) {
                $keywords = array_merge($keywords, $this->getYahooKeywords($yahooID, $product['description']));
            }
            foreach ($keywords as $keyword) {
                $finalKeywords[] = $this->makeSlugs(trim($keyword), 0, false, $source_langcode);
            }
            $finalKeywords = array_filter(array_unique($finalKeywords));
            $finalKeywords = implode(', ', $finalKeywords);
            $this->db->query("UPDATE " . DB_PREFIX . "product_description SET meta_keyword = '" . $this->db->escape($finalKeywords) . "' where product_id = " . (int)$product['product_id'] . " and language_id = " . (int)$product['language_id']);
        }
    }

    private function getYahooKeywords($yahooID, $description) {
        $keywords = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'appid=' . $yahooID . '&output=php&context=' . urlencode(strip_tags(html_entity_decode($description, ENT_QUOTES, "UTF-8"))));
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output) {
            $output = unserialize($output);
            if (isset($output['ResultSet']['Result'])) {
                $keywords = $output['ResultSet']['Result'];
                if (!is_array($keywords)) {
                    $keywords = array($keywords);
                }
            }
        }
        return $keywords;
    }

    public function generateTags($template, $source_langcode) {
        $products = $this->getProductsForMetaKeywords();
        $slugs = array();
        foreach ($products as $product) {
            $finalCategories = array();
            $categories = $this->getProductCategories($product['product_id'], $product['language_id']);
            foreach ($categories as $category) {
                $finalCategories[] = $category['name'];
            }
            $tags = array('[product_name]' => $product['name'],
                          '[model_name]' => $product['model'],
                          '[manufacturer_name]' => $product['manufacturer_name'],
                          '[categories_names]' => implode(',', $finalCategories)

            );
            $finalKeywords = array();
            $keywords = explode(',', strtr($template, $tags));
            foreach ($keywords as $keyword) {
                $finalKeywords[] =  $this->makeSlugs(trim($keyword), 0, false, $source_langcode);
            }
            $finalKeywords = array_filter(array_unique($finalKeywords));
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id = " . (int)$product['product_id'] . " and language_id = " . (int)$product['language_id']);
            foreach ($finalKeywords as $keyword) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = " . (int)$product['product_id'] . ", tag = '" . $this->db->escape($keyword) . "', language_id = " . (int)$product['language_id']);
            }
        }
    }

    private function getCategories() {
        $query = $this->db->query("SELECT c.category_id, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
        return $query->rows;
    }

    private function getProductCategories($productId, $languageId) {
        $query = $this->db->query("SELECT c.category_id, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) INNER JOIN " . DB_PREFIX . "product_to_category pc ON (pc.category_id = c.category_id) WHERE cd.language_id = " . (int)$languageId . " AND pc.product_id = " . (int)$productId . " ORDER BY c.sort_order, cd.name ASC");
        return $query->rows;
    }

    private function getProducts() {
        $query = $this->db->query("SELECT p.product_id, pd.name, p.model, m.name as manufacturer_name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pd.name ASC");
        return $query->rows;
    }


    private function getManufacturers() {
        $query = $this->db->query("SELECT m.manufacturer_id, m.name FROM " . DB_PREFIX . "manufacturer m ORDER BY m.name ASC");
        return $query->rows;
    }

    private function getProductsForMetaKeywords() {
        $query = $this->db->query("SELECT p.product_id, pd.name, p.model, m.name as manufacturer_name, pd.description, pd.language_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) ORDER BY pd.name ASC");
        return $query->rows;
    }

    public function getLanguages() {
        $query = $this->db->query("SELECT substring(code,1,2) as code, name FROM " . DB_PREFIX . "language");
        return $query->rows;
    }

    // Taken from http://code.google.com/p/php-slugs/
    private function my_str_split($string) {
        $slen = strlen($string);
        for ($i = 0; $i < $slen; $i++) {
            $sArray[$i] = $string{$i};
        }
        return $sArray;
    }


    /**
     * Transliterates UTF-8 encoded text to US-ASCII.
     *
     * Based on Mediawiki's UtfNormal::quickIsNFCVerify().
     *
     * @param $string
     *   UTF-8 encoded text input.
     * @param $unknown
     *   Replacement string for characters that do not have a suitable ASCII
     *   equivalent.
     * @param $source_langcode
     *   Optional ISO 639 language code that denotes the language of the input and
     *   is used to apply language-specific variations. If the source language is
     *   not known at the time of transliteration, it is recommended to set this
     *   argument to the site default language to produce consistent results.
     *   Otherwise the current display language will be used.
     * @return
     *   Transliterated text.
     */
    function _transliteration_process($string, $unknown = '?', $source_langcode = NULL) {
        // ASCII is always valid NFC! If we're only ever given plain ASCII, we can
        // avoid the overhead of initializing the decomposition tables by skipping
        // out early.
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        static $tailBytes;

        if (!isset($tailBytes)) {
            // Each UTF-8 head byte is followed by a certain number of tail bytes.
            $tailBytes = array();
            for ($n = 0; $n < 256; $n++) {
                if ($n < 0xc0) {
                    $remaining = 0;
                }
                elseif ($n < 0xe0) {
                    $remaining = 1;
                }
                elseif ($n < 0xf0) {
                    $remaining = 2;
                }
                elseif ($n < 0xf8) {
                    $remaining = 3;
                }
                elseif ($n < 0xfc) {
                    $remaining = 4;
                }
                elseif ($n < 0xfe) {
                    $remaining = 5;
                }
                else {
                    $remaining = 0;
                }
                $tailBytes[chr($n)] = $remaining;
            }
        }

        // Chop the text into pure-ASCII and non-ASCII areas; large ASCII parts can
        // be handled much more quickly. Don't chop up Unicode areas for punctuation,
        // though, that wastes energy.
        preg_match_all('/[\x00-\x7f]+|[\x80-\xff][\x00-\x40\x5b-\x5f\x7b-\xff]*/', $string, $matches);

        $result = '';
        foreach ($matches[0] as $str) {
            if ($str[0] < "\x80") {
                // ASCII chunk: guaranteed to be valid UTF-8 and in normal form C, so
                // skip over it.
                $result .= $str;
                continue;
            }

            // We'll have to examine the chunk byte by byte to ensure that it consists
            // of valid UTF-8 sequences, and to see if any of them might not be
            // normalized.
            //
            // Since PHP is not the fastest language on earth, some of this code is a
            // little ugly with inner loop optimizations.

            $head = '';
            $chunk = strlen($str);
            // Counting down is faster. I'm *so* sorry.
            $len = $chunk + 1;

            for ($i = -1; --$len;) {
                $c = $str[++$i];
                if ($remaining = $tailBytes[$c]) {
                    // UTF-8 head byte!
                    $sequence = $head = $c;
                    do {
                        // Look for the defined number of tail bytes...
                        if (--$len && ($c = $str[++$i]) >= "\x80" && $c < "\xc0") {
                            // Legal tail bytes are nice.
                            $sequence .= $c;
                        }
                        else {
                            if ($len == 0) {
                                // Premature end of string! Drop a replacement character into
                                // output to represent the invalid UTF-8 sequence.
                                $result .= $unknown;
                                break 2;
                            }
                            else {
                                // Illegal tail byte; abandon the sequence.
                                $result .= $unknown;
                                // Back up and reprocess this byte; it may itself be a legal
                                // ASCII or UTF-8 sequence head.
                                --$i;
                                ++$len;
                                continue 2;
                            }
                        }
                    } while (--$remaining);

                    $n = ord($head);
                    if ($n <= 0xdf) {
                        $ord = ($n - 192) * 64 + (ord($sequence[1]) - 128);
                    }
                    elseif ($n <= 0xef) {
                        $ord = ($n - 224) * 4096 + (ord($sequence[1]) - 128) * 64 + (ord($sequence[2]) - 128);
                    }
                    elseif ($n <= 0xf7) {
                        $ord = ($n - 240) * 262144 + (ord($sequence[1]) - 128) * 4096 + (ord($sequence[2]) - 128) * 64 + (ord($sequence[3]) - 128);
                    }
                    elseif ($n <= 0xfb) {
                        $ord = ($n - 248) * 16777216 + (ord($sequence[1]) - 128) * 262144 + (ord($sequence[2]) - 128) * 4096 + (ord($sequence[3]) - 128) * 64 + (ord($sequence[4]) - 128);
                    }
                    elseif ($n <= 0xfd) {
                        $ord = ($n - 252) * 1073741824 + (ord($sequence[1]) - 128) * 16777216 + (ord($sequence[2]) - 128) * 262144 + (ord($sequence[3]) - 128) * 4096 + (ord($sequence[4]) - 128) * 64 + (ord($sequence[5]) - 128);
                    }
                    $result .= $this->_transliteration_replace($ord, $unknown, $source_langcode);
                    $head = '';
                }
                elseif ($c < "\x80") {
                    // ASCII byte.
                    $result .= $c;
                    $head = '';
                }
                elseif ($c < "\xc0") {
                    // Illegal tail bytes.
                    if ($head == '') {
                        $result .= $unknown;
                    }
                }
                else {
                    // Miscellaneous freaks.
                    $result .= $unknown;
                    $head = '';
                }
            }
        }
        return $result;
    }

    /**
     * Replaces a Unicode character using the transliteration database.
     *
     * @param $ord
     *   An ordinal Unicode character code.
     * @param $unknown
     *   Replacement string for characters that do not have a suitable ASCII
     *   equivalent.
     * @param $langcode
     *   Optional ISO 639 language code that denotes the language of the input and
     *   is used to apply language-specific variations.  Defaults to the current
     *   display language.
     * @return
     *   ASCII replacement character.
     */
    function _transliteration_replace($ord, $unknown = '?', $langcode = NULL) {
        static $map = array();

        $bank = $ord >> 8;

        if (!isset($map[$bank][$langcode])) {
            $file = dirname(__FILE__) . '/deadcow_seo_data/' . sprintf('x%02x', $bank) . '.php';
            if (file_exists($file)) {
                include $file;
                if ($langcode != 'en' && isset($variant[$langcode])) {
                    // Merge in language specific mappings.
                    $map[$bank][$langcode] = $variant[$langcode] + $base;
                }
                else {
                    $map[$bank][$langcode] = $base;
                }
            }
            else {
                $map[$bank][$langcode] = array();
            }
        }

        $ord = $ord & 255;

        return isset($map[$bank][$langcode][$ord]) ? $map[$bank][$langcode][$ord] : $unknown;
    }

    private function makeSlugs($string, $maxlen = 0, $noSpace = true, $source_langcode = null) {
        global $session;
        $newStringTab = array();
        $string = strtolower($this->_transliteration_process(trim(html_entity_decode($string, ENT_QUOTES, "UTF-8")), '-', $source_langcode));
        if (function_exists('str_split')) {
            $stringTab = str_split($string);
        } else {
            $stringTab = $this->my_str_split($string);
        }
        $numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-");
        foreach ($stringTab as $letter) {
            if (in_array($letter, range("a", "z")) || in_array($letter, $numbers)) {
                $newStringTab[] = $letter;
            } elseif ($letter == " ") {
                if ($noSpace) {
                    $newStringTab[] = "-";
                } else {
                    $newStringTab[] = " ";
                }
            }
        }
        if (count($newStringTab)) {
            $newString = implode($newStringTab);
            if ($maxlen > 0) {
                $newString = substr($newString, 0, $maxlen);
            }
            $newString = $this->removeDuplicates('--', '-', $newString);
        } else {
            $newString = '';
        }
        return $newString;
    }

    private function removeDuplicates($sSearch, $sReplace, $sSubject) {
        $i = 0;
        do {
            $sSubject = str_replace($sSearch, $sReplace, $sSubject);
            $pos = strpos($sSubject, $sSearch);
            $i++;
            if ($i > 100) {
                die('removeDuplicates() loop error');
            }
        } while ($pos !== false);
        return $sSubject;
    }
}