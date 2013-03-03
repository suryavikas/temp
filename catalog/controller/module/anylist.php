<?php  
class ControllerModuleAnylist extends Controller {
	protected function index($setting) {
		$languageId = $this->config->get('config_language_id');

		$this->data['titlelink'] = $setting['titlelink'];
   		$this->data['heading_title'] = $setting['title'][$languageId];
		
		$this->data['button_cart'] = $this->language->get('button_cart');

		$this->load->model('catalog/anylist');
		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');
		
		$this->data['products'] = array();
		$this->data['latest'] = (isset($setting['latest'])) ? (int) $setting['latest'] : 0;
		$this->data['specials'] = (isset($setting['specials'])) ? (int) $setting['specials'] : 0;
		$this->data['grid'] = (isset($setting['view'])) ? ((int) $setting['view'] > 0): false;

        $limitcat = (isset($setting['limitcategory'])) ? $setting['limitcategory'] : '';		
        $limitprm = (isset($setting['limitproduct_manufacturer'])) ? $setting['limitproduct_manufacturer'] : '';		
        $limitprc = (isset($setting['limitproduct_category'])) ? $setting['limitproduct_category'] : '';		
        $limitman = (isset($setting['limitmanufacturer'])) ? $setting['limitmanufacturer'] : '';		

        $routes = $this->model_catalog_anylist->getLayoutRoutes($setting['layout_id']);

	    $filter = false;
		foreach($routes as $route)
	        if (stripos($route['route'],'product/category')!==false and stripos($this->request->get['route'],'product/category')!==false)
	            $filter = 'category'; 
	        elseif (stripos($route['route'],'product/manufacturer')!==false and stripos($this->request->get['route'],'product/manufacturer')!==false)
	            $filter = 'manufacturer'; 
	        elseif (stripos($route['route'],'product/product')!==false and stripos($this->request->get['route'],'product/product')!==false)
	            $filter = 'product'; 
	        else
	            $filter = false;
            
         $ok = true;

		if ($filter=='category' and is_array($limitcat) and count($limitcat)>0 and isset($this->request->get['path'])) {
			$path = explode("_",$this->request->get['path']);
			$curr_cat = $path[count($path)-1];
			$ok = ($ok and array_search($curr_cat,$limitcat)!==false);
		}		
		 
		if ($filter=='manufacturer' and is_array($limitman) and count($limitman)>0 and isset($this->request->get['manufacturer_id'])) {
			$curr_man = (int) $this->request->get['manufacturer_id'];
			$ok = ($ok and array_search($curr_man,$limitman)!==false);
		}		
		 
		if ($filter=='product' and ((is_array($limitprm) and count($limitprm)>0) or (is_array($limitprc) and count($limitprc)>0)) and isset($this->request->get['product_id'])) {
			$curr_pro = (int) $this->request->get['product_id'];
			$myProduct = $this->model_catalog_product->getProduct($curr_pro);
			if ($ok and is_array($limitprm) and count($limitprm)>0)
				$ok = ($ok and array_search($myProduct['manufacturer_id'],$limitprm)!==false);
				
			if ($ok and is_array($limitprc) and count($limitprc)>0) {
				$rc = $this->model_catalog_product->getCategories($curr_pro);

				// If ANY of product's category exists in filter  
				foreach($rc as $c) {
					$ok = ($ok OR array_search($c['category_id'],$limitprc)!==false);
				} 

				// If EXACT of product's categories exists in filter  
				//foreach($rc as $c) {
				//	$ok = ($ok AND array_search($c['category_id'],$limitprc)!==false);
				//} 
			}
		}
		
		$outofperiod = false;
		
		if (isset($setting['date_start']) and strtotime($setting['date_start'])>time()) {
			$outofperiod = true;		
		}

		if (isset($setting['date_end']) and strtotime($setting['date_end'])<time()) {
			$outofperiod = true;		
		}

		if ((!$filter or $ok) and !$outofperiod) {
			$category = (isset($setting['category'])) ? $setting['category'] : array(); 
			$products = explode(",",$setting['products']);
			// SORTING OF LIST:
			//	 'sort' is field of product table (only product table - no names, no descriptions)
			//   'order' is ASC / DESC order
			$products = $this->model_catalog_anylist->getProductsID( array(
																			'category'=>$category, 
																			'products'=>$products, 
																			'latest'=>$this->data['latest'],
																			'specials'=>$this->data['specials'],
																			'date_start'=>(isset($setting['date_start']) ? $setting['date_start'] : ''),
																			'date_end'=>(isset($setting['date_end']) ? $setting['date_end'] : ''),
																			'sort'=>(isset($setting['sortfield']) ? $setting['sortfield'] : ''),
																			'order'=>(isset($setting['sortorder']) ? $setting['sortorder'] : '') 
																			) 
																	);

			$limit = ($setting['limit']>count($products)) ? count($products) : (int) $setting['limit']; 
					
	        if (count($products)<$limit) {
	            $results = array_keys($products);
	        } else  {
	    		$results =  ($limit>1) ? array_rand($products,$limit) : array( rand(0,count($products)-1) );
	        }
			foreach ($results as $pid) {
				$result = ($pid and isset($products[$pid]) and $products[$pid]) ? $this->model_catalog_product->getProduct($products[$pid]) : false;

				if ($result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
					} else {
						$image = false;
					}
								
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = false;
					}
							
					if ((float)$result['special']) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$special = false;
					}
					
					if ($this->config->get('config_review_status')) {
						$rating = $result['rating'];
					} else {
						$rating = false;
					}
					
					$this->data['products'][] = array(
						'product_id' => $result['product_id'],
						'thumb'   	 => $image,
						'name'    	 => $result['name'],
						'price'   	 => $price,
						'special' 	 => $special,
						'rating'     => $rating,
						'description'=> $result['description'],
						'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
						'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					);
				}
			}
		} else {
			$this->data['products'] = array();
		}  
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/anylist.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/anylist.tpl';
		} else {
			$this->template = 'default/template/module/anylist.tpl';
		}
		
		$this->render();
	}
}
?>