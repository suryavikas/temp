<?php

class ControllerModuleFilters extends Controller {

    protected function index($setting) {
        $this->language->load('module/filters');
        $categoryId = 0;
        if (isset($this->request->get['path'])) {
            $parts = explode('_', $this->request->get['path']);
            $size = sizeof($parts);
            $categoryId = $parts[--$size];
            $this->data['categoryId'] = $categoryId;
        }

        if (isset($this->request->get['filters'])) {
            $temp = htmlspecialchars_decode($this->request->get['filters']);
            $filters = json_decode($temp, true);
            $this->data['filters'] = $filters;
        }
        $this->load->model('filters/filters');
        //Manufacturers
        $resultSet = $this->model_filters_filters->getManufacturersInACategory($categoryId);
        $this->data['manufacturers'] = $resultSet;
        unset($resultSet);
        //Product option filters
        $resultArray = $this->model_filters_filters->getProductOptionsInACategory($categoryId);
        $this->data['productOptions'] = $resultArray;
        //Price filter
        $priceRangeArray = $this->model_filters_filters->getPriceRange($categoryId);
        $this->data['priceRangeArray'] = $priceRangeArray;
//         echo "<pre>";
//        print_r($_SESSION);
//         var_dump($priceRangeArray);
        //Setting text from language file
        $this->data['text_manufacturer_select_option'] = html_entity_decode($this->language->get('text_manufacturer_select_option'));
        $this->data['text_product_options_select_option'] = html_entity_decode($this->language->get('text_product_options_select_option'));
        $this->data['text_price_filter'] = html_entity_decode($this->language->get('text_price_filter'));
//        $this->data['currency'] = html_entity_decode($this->language->get('text_currency'));
        $this->data['currency'] = html_entity_decode($_SESSION["currency"]);
        $this->data['text_sale_items'] = html_entity_decode($this->language->get('text_sale_items'));
        $this->data['text_in_stock_products'] = html_entity_decode($this->language->get('text_in_stock_products'));


        $this->data['heading_title'] = html_entity_decode($this->language->get('heading_title'), ENT_QUOTES, 'UTF-8');



        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/filters.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/filters.tpl';
        } else {
            $this->template = 'default/template/module/filters.tpl';
        }

        $this->render();
    }

    public function filter() {
        $filters = null;
        $this->language->load('module/filters');
        $this->load->model('filters/filters');

        if (isset($this->request->post['filters'])) {
            $filters = $this->request->post['filters'];
        }
        $this->model_filters_filters->filterProducts($filters);

        echo('HELLO');
    }

    public function applyFilter() {
        $this->language->load('product/category');

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

		$this->load->model('filters/filters');

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.sort_order';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        if (isset($this->request->get['path'])) {
            $path = '';

            $parts = explode('_', (string) $this->request->get['path']);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = $path_id;
                } else {
                    $path .= '_' . $path_id;
                }

                $category_info = $this->model_catalog_category->getCategory($path_id);

                if ($category_info) {
                    $this->data['breadcrumbs'][] = array(
                        'text' => $category_info['name'],
                        'href' => $this->url->link('product/category', 'path=' . $path),
                        'separator' => $this->language->get('text_separator')
                    );
                }
            }

            $category_id = array_pop($parts);
        } else {
            $category_id = 0;
        }

        $category_info = $this->model_catalog_category->getCategory($category_id);

        if ($category_info) {
            $this->document->setTitle($category_info['name']);
            $this->document->setDescription($category_info['meta_description']);
            $this->document->setKeywords($category_info['meta_keyword']);

            $this->data['heading_title'] = $category_info['name'];

            $this->data['text_refine'] = $this->language->get('text_refine');
            $this->data['text_empty'] = $this->language->get('text_empty');
            $this->data['text_quantity'] = $this->language->get('text_quantity');
            $this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $this->data['text_model'] = $this->language->get('text_model');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_tax'] = $this->language->get('text_tax');
            $this->data['text_points'] = $this->language->get('text_points');
            $this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
            $this->data['text_display'] = $this->language->get('text_display');
            $this->data['text_list'] = $this->language->get('text_list');
            $this->data['text_grid'] = $this->language->get('text_grid');
            $this->data['text_sort'] = $this->language->get('text_sort');
            $this->data['text_limit'] = $this->language->get('text_limit');

            $this->data['button_cart'] = $this->language->get('button_cart');
            $this->data['button_wishlist'] = $this->language->get('button_wishlist');
            $this->data['button_compare'] = $this->language->get('button_compare');
            $this->data['button_continue'] = $this->language->get('button_continue');

//            if ($category_info['image']) {
//                $this->data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
//            } else {
//                $this->data['thumb'] = '';
//            }
//            $this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
            $this->data['compare'] = $this->url->link('product/compare');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['categories'] = array();

//            $results = $this->model_catalog_category->getCategories($category_id);
//
//            foreach ($results as $result) {
//                $data = array(
//                    'filter_category_id' => $result['category_id'],
//                    'filter_sub_category' => true
//                );
//
//                $product_total = $this->model_catalog_product->getTotalProducts($data);
//
//                $this->data['categories'][] = array(
//                    'name' => $result['name'] . ' (' . $product_total . ')',
//                    'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
//                );
//            }

            $this->data['products'] = array();

            $data = array(
                'filter_category_id' => $category_id,
                'sort' => $sort,
                'order' => $order,
                'start' => ($page - 1) * $limit,
                'limit' => $limit
            );            
            /*             * ******************************************TBD as VQMOD**************************************************** */
            $filters = array();
            if (isset($this->request->post['filters'])) {
                $filters = $this->request->post['filters'];

            }
        
            //Add by Surya for Other filter information
            $manufacturerId = 0;
            $productOption = null;
            $saleItems = null;
            $inStock = null;
            $minPrice = null;
            $maxPrice = null;
            ksort($filters);
            $localPriceArray = array();
            foreach ($filters as $filter) {
  //          print_r($filter);
			//	echo "<br>****".$filter['param']."*******".$filter['val']."</br>";
                if (strtolower($filter['param']) == 'product-option') {
                    if (!$productOption) {
                        $productOption = $filter['val'];
                    } else {
                        $productOption = $productOption . ',' . $filter['val'];
                    }
                } else if (strtolower($filter['param']) == 'manufacturer') {
                    if (!$manufacturerId) {
                        $manufacturerId = $filter['val'];
                    } else {
                        $manufacturerId = $manufacturerId . ',' . $filter['val'];
                    }
                } else if (strtolower($filter['param']) == 'sale_items' && strtolower($filter['val']) == 'on') {
					echo "@@@@@@@@@@@@@@@222";
                    $saleItems = true;
                } else if (strtolower($filter['param']) == 'in_stock' && strtolower($filter['val']) == 'on') {
					echo "$$$$$$$$$$$";
                    $inStock = true;
                } else if (strtolower($filter['param']) == 'price') {
                    list($minPrice, $maxPrice) = explode("-", $filter['val']);
                    array_push($localPriceArray,$minPrice);
                    array_push($localPriceArray,$maxPrice);
                }
            }

            if(sizeof($localPriceArray) > 1){
                $minPrice = min($localPriceArray);
                $maxPrice = max($localPriceArray);
            }
            //Converting data to array
            $filterCond = array(
                'productOption' => $productOption,
                'manufacturerId' => $manufacturerId,
                'saleItems' => $saleItems,
                'inStock' => $inStock,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice
            );
       // print_r($filterCond);
            $data = array_merge($data, $filterCond);
//        echo'<pre>';
        
       
            /*             * ******************************************TBD as VQMOD**************************************************** */
			
            $product_total = $this->model_filters_filters->getTotalProducts($data);
			$results = $this->model_filters_filters->getProductsResult($data);

			//echo "<pre>";
			//print_r($data);
			//echo "Products total abc ".$product_total;
			//echo "</pre>";
//            $results = $this->model_catalog_product->getProducts($data);
            
            

            foreach ($results as $result) {
//                print_r($result);
//                die;
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                } else {
                    $image = false;
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                }

                if ((float) $result['special']) {
                    $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $special = false;
                }

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price']);
                } else {
                    $tax = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = (int) $result['rating'];
                } else {
                    $rating = false;
                }

                $this->data['products'][] = array(
                    'product_id' => $result['product_id'],
                    'thumb' => $image,
                    'name' => $result['name'],
                    'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
                    'price' => $price,
                    'special' => $special,
                    'tax' => $tax,
                    'rating' => $result['rating'],
                    'reviews' => sprintf($this->language->get('text_reviews'), (int) $result['reviews']),
                    'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
                );
            }

            $url = '';

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['sorts'] = array();

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_default'),
                'value' => 'p.sort_order-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_name_asc'),
                'value' => 'pd.name-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_name_desc'),
                'value' => 'pd.name-DESC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_price_asc'),
                'value' => 'p.price-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_price_desc'),
                'value' => 'p.price-DESC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
            );

            if ($this->config->get('config_review_status')) {
                $this->data['sorts'][] = array(
                    'text' => $this->language->get('text_rating_desc'),
                    'value' => 'rating-DESC',
                    'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
                );

                $this->data['sorts'][] = array(
                    'text' => $this->language->get('text_rating_asc'),
                    'value' => 'rating-ASC',
                    'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
                );
            }

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_model_asc'),
                'value' => 'p.model-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_model_desc'),
                'value' => 'p.model-DESC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
            );

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->data['limits'] = array();

            $this->data['limits'][] = array(
                'text' => $this->config->get('config_catalog_limit'),
                'value' => $this->config->get('config_catalog_limit'),
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $this->config->get('config_catalog_limit'))
            );

            $this->data['limits'][] = array(
                'text' => 25,
                'value' => 25,
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=25')
            );

            $this->data['limits'][] = array(
                'text' => 50,
                'value' => 50,
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=50')
            );

            $this->data['limits'][] = array(
                'text' => 75,
                'value' => 75,
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=75')
            );

            $this->data['limits'][] = array(
                'text' => 100,
                'value' => 100,
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=100')
            );

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $pagination = new Pagination();
            $pagination->total = $product_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

            $this->data['pagination'] = $pagination->render();

            $this->data['sort'] = $sort;
            $this->data['order'] = $order;
            $this->data['limit'] = $limit;

            $this->data['continue'] = $this->url->link('common/home');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/filtered_category.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/filtered_category.tpl';
            } else {
                $this->template = 'default/template/module/filtered_category.tpl';
            }

//            $this->children = array(
//                'common/column_left',
//                'common/column_right',
//                'common/content_top',
//                'common/content_bottom',
//                'common/footer',
//                'common/header'
//            );

            $this->response->setOutput($this->render());
        } else {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('product/category', $url),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');

            $this->data['text_error'] = $this->language->get('text_error');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('common/home');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
            } else {
                $this->template = 'default/template/error/not_found.tpl';
            }

//            $this->children = array(
//                'common/column_left',
//                'common/column_right',
//                'common/content_top',
//                'common/content_bottom',
//                'common/footer',
//                'common/header'
//            );

            $this->response->setOutput($this->render());
        }
    }

}

?>