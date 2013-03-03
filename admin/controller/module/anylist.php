<?php
class ControllerModuleAnylist extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/anylist');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('catalog/product');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			//echo "<xmp>";print_r($this->request->post['anylist_module']);die();			
			$this->model_setting_setting->editSetting('anylist', array( 'anylist_module' => $this->request->post['anylist_module']));		
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		$this->data['text_select_all'] = $this->language->get('text_select_all');
		$this->data['text_unselect_all'] = $this->language->get('text_unselect_all');
		$this->data['text_view_grid'] = $this->language->get('text_view_grid');
		$this->data['text_view_list'] = $this->language->get('text_view_list');
		$this->data['text_period'] = $this->language->get('text_period');
		
		$this->data['entry_dimension'] = $this->language->get('entry_dimension');
		$this->data['entry_code'] = $this->language->get('entry_code');
		$this->data['entry_limit'] = $this->language->get('entry_limit');
		$this->data['entry_title'] = $this->language->get('entry_title');
		$this->data['entry_titlelink'] = $this->language->get('entry_titlelink');
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_category'] = $this->language->get('entry_category');
		$this->data['entry_products'] = $this->language->get('entry_products');
		$this->data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$this->data['entry_limit_help'] = $this->language->get('entry_limit_help');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_selection'] = $this->language->get('entry_selection');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_sort_descending'] = $this->language->get('entry_sort_descending');
		$this->data['entry_latest_text'] = $this->language->get('entry_latest_text');
		$this->data['entry_latest_products'] = $this->language->get('entry_latest_products');
		$this->data['entry_specials_text'] = $this->language->get('entry_specials_text');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['dimension'])) {
			$this->data['error_dimension'] = $this->error['dimension'];
		} else {
			$this->data['error_dimension'] = array();
		}
				
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/anylist', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/anylist', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];
		$this->data['modules'] = array();
		
		if (isset($this->request->post['anylist_module'])) {
			$this->data['modules'] = $this->request->post['anylist_module'];
		} elseif ($this->config->get('anylist_module')) { 
			$this->data['modules'] = $this->config->get('anylist_module');
		}	
				
		for($i=0; $i<count($this->data['modules']); $i++) {
			$this->data['modules'][$i]['products_list'] = array();
			if ($this->data['modules'][$i]['products']) {
				foreach(explode(",",$this->data['modules'][$i]['products']) as $pid) {
					$p = $this->model_catalog_product->getProduct($pid);
					if ($p)
						$this->data['modules'][$i]['products_list'][] = $p; 
				}
			}
		}		

		$this->data['prodfields'] = array();
		//  GET product table structure
		$p = $this->model_catalog_product->getProducts(array('start'=>0,'limit'=>1));
		if ($p[0]) {
			foreach($p[0] as $f=>$v) {
				$this->data['prodfields'][] = $f;
				if ($f=='viewed') break;
			}
		}

		$this->load->model('catalog/category');
		$this->data['categories'] = $this->model_catalog_category->getCategories(0);

		$this->load->model('catalog/manufacturer');
		$this->data['manufacturers'] = $results = $this->model_catalog_manufacturer->getManufacturers();

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
        for($i=0; $i<count($this->data['layouts']); $i++) {
            $routes = $this->model_design_layout->getLayoutRoutes($this->data['layouts'][$i]['layout_id']);
            $this->data['layouts'][$i]['filter'] = '';
            $this->data['layouts'][$i]['routes'] = array();
            foreach($routes as $route) {
                $this->data['layouts'][$i]['routes'][] = $route['route'];
                if (stripos($route['route'],'product/product')!==false) 
                    $this->data['layouts'][$i]['filter'] = 'product';
                elseif (stripos($route['route'],'product/category')!==false)
                    $this->data['layouts'][$i]['filter'] = 'category';
                elseif (stripos($route['route'],'product/manufacturer')!==false)
                    $this->data['layouts'][$i]['filter'] = 'manufacturer';
            }
        }

		$this->template = 'module/anylist.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/anylist')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['anylist_module'])) {
			foreach ($this->request->post['anylist_module'] as $key => $value) {
				if (!$value['width'] || !$value['height']) {
					$this->error['dimension'][$key] = $this->language->get('error_dimension');
				}			
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>