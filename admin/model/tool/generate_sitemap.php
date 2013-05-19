<?php
class ModelToolGenerateSitemap extends Model {	
	
	public function generate() {
		$this->load->language('tool/generate_sitemap');
		
		$output = '';
		
		//Generating sitemaps for categories.
		$fp = fopen("../sitemaps/sitemapcategories.xml", "w+");
		fwrite($fp, "<?xml-stylesheet type=\"text/xsl\" href=\"gss.xsl\"?>\r\n");
		fwrite($fp, "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\r\n");				
		fwrite($fp, $this->getCategories(0));		
		fwrite($fp, "</urlset>");
		fclose($fp);
		$output .= "<a href='" . HTTP_CATALOG . "sitemaps/sitemapcategories.xml' target='_blank'>" . HTTP_CATALOG . "sitemaps/sitemapcategories.xml</a> generated successfully.<br><br>";
		
		//Generating sitemaps for products.
		$fp = fopen("../sitemaps/sitemapproducts.xml", "w+");
		fwrite($fp, "<?xml-stylesheet type=\"text/xsl\" href=\"gss.xsl\"?>\r\n");
		fwrite($fp, "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\r\n");				
		fwrite($fp, $this->getProducts());		
		fwrite($fp, "</urlset>");
		fclose($fp);
		$output .= "<a href='" . HTTP_CATALOG . "sitemaps/sitemapproducts.xml' target='_blank'>" . HTTP_CATALOG . "sitemaps/sitemapproducts.xml</a> generated successfully.<br><br>";
		
		//Generating sitemaps for other pages.
		$fp = fopen("../sitemaps/sitemappages.xml", "w+");
		fwrite($fp, "<?xml-stylesheet type=\"text/xsl\" href=\"gss.xsl\"?>\r\n");
		fwrite($fp, "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\r\n");				
		fwrite($fp, $this->getInformationPages());		
		fwrite($fp, "</urlset>");
		fclose($fp);
		$output .= "<a href='" . HTTP_CATALOG . "sitemaps/sitemappages.xml' target='_blank'>" . HTTP_CATALOG . "sitemaps/sitemappages.xml</a> generated successfully.<br><br>";
		
		$output .= $this->language->get('text_success');
		
		return $output;
	}
	
	private function get_formatted_file_name($filecount)
	{
		$fname = "sitemap_";
		if ($filecount<10)
			$fname .= "0";
		$fname .= $filecount;
		$fname .= ".xml";
		return $fname;
	}
	
	protected function getCategories($parent_id, $current_path = '') {
		$output = '';
		
		$this->load->model('catalog/category');
		
		$results = $this->model_catalog_category->getSiteMapCategories($parent_id);
		
		foreach ($results as $result) {	
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			
			$output .= $this->generateLinkNode(HTTP_CATALOG . 'index.php?route=product/category&path=' . $new_path);
			
        	$output .= $this->getCategories($result['category_id'], $new_path);
		}
		
		return $output;
	}
	
	protected function getProducts() {
		$output = '';
		
		$this->load->model('catalog/product');
		
		$results = $this->model_catalog_product->getAllProducts();
		
		foreach ($results as $result) {	
			$output .= $this->generateLinkNode(HTTP_CATALOG . 'index.php?route=product/product&product_id=' . $result['product_id'], "weekly", "0.5");
		}
		
		return $output;
	}
	
	protected function getInformationPages() {
		$output = '';
		
		$this->load->model('catalog/information');
		
		foreach ($this->model_catalog_information->getInformations() as $result) {
			$output .= $this->generateLinkNode(HTTP_CATALOG .  'index.php?route=information/information&information_id=' . $result['information_id']);
		}
		$output .= $this->generateLinkNode(HTTP_CATALOG . 'index.php?route=information/contact', "weekly", "0.5");
		
		return $output;
	}
	
	protected function generateLinkNode($link, $changefreq = 'yearly', $priority = '1.0') {
		$this->load->model('tool/seo_url');
		
		$link = $this->model_tool_seo_url->rewrite($link);
		
		$link = str_replace("&","&amp;", $link);
		
		$output = "";	
		$output .= "    <url>\r\n";
		$output .= "        <loc>" . $link . "</loc>\r\n";
		$output .= "        <lastmod>" . date("Y-m-d") . "</lastmod>\r\n";
		$output .= "        <changefreq>" . $changefreq . "</changefreq>\r\n";
		$output .= "        <priority>" . $priority . "</priority>\r\n";
		$output .= "    </url>\r\n";
		
		return $output;
	}
}
?>