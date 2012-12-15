<?php
class ControllerShippingFlatplusfree extends Controller {
	private $error = array(); 
	
	public function index() {   
		
		$this->data['current_version']='3.0';
		
		$this->load->language('shipping/flatplusfree');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('flatplusfree', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['entry_total'] = $this->language->get('entry_total');
		$this->data['entry_cost'] = $this->language->get('entry_cost');
		$this->data['entry_tax'] = $this->language->get('entry_tax');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['tab_contact'] = $this->language->get('tab_contact');
				
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_shipping'),
			'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('shipping/flatplusfree', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('shipping/flatplusfree', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');
		
	$this->load->model('localisation/geo_zone');
	//$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
	$geo_zones = $this->model_localisation_geo_zone->getGeoZones();
	
	
	foreach ($geo_zones as $geo_zone) {
			if (isset($this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_rate'])) {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_rate'];
			} else {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->config->get('flatplusfree_' . $geo_zone['geo_zone_id'] . '_rate');
			}		
			
			if (isset($this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_status'])) {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_status'] = $this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_status'];
			} else {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_status'] = $this->config->get('flatplusfree_' . $geo_zone['geo_zone_id'] . '_status');
			}		
		
		
		if (isset($this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_cost'])) {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_cost'] = $this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_cost'];
			} else {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_cost'] = $this->config->get('flatplusfree_' . $geo_zone['geo_zone_id'] . '_cost');
			}	
		
		if (isset($this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_tax_class_id'])) {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_tax_class_id'] = $this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_tax_class_id'];
			} else {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_tax_class_id'] = $this->config->get('flatplusfree_' . $geo_zone['geo_zone_id'] . '_tax_class_id');
			}	
		
		
		if (isset($this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_geo_zone_id'])) {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_geo_zone_id'] = $this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_geo_zone_id'];
			} else {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_geo_zone_id'] = $this->config->get('flatplusfree_' . $geo_zone['geo_zone_id'] . '_geo_zone_id');
			}	
		
			if (isset($this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_sort_order'])) {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_sort_order'] = $this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_sort_order'];
			} else {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_sort_order'] = $this->config->get('flatplusfree_' . $geo_zone['geo_zone_id'] . '_sort_order');
			}	
		
		if (isset($this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_free_total'])) {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_free_total'] = $this->request->post['flatplusfree_' . $geo_zone['geo_zone_id'] . '_free_total'];
			} else {
				$this->data['flatplusfree_' . $geo_zone['geo_zone_id'] . '_free_total'] = $this->config->get('flatplusfree_' . $geo_zone['geo_zone_id'] . '_free_total');
			}	
		}
		
		$this->data['geo_zones'] = $geo_zones;
	
	
	
	if (isset($this->request->post['flatplusfree_status'])) {
			$this->data['flatplusfree_status'] = $this->request->post['flatplusfree_status'];
		} else {
			$this->data['flatplusfree_status'] = $this->config->get('flatplusfree_status');
		}
		
		if (isset($this->request->post['flatplusfree_sort_order'])) {
			$this->data['flatplusfree_sort_order'] = $this->request->post['flatplusfree_sort_order'];
		} else {
			$this->data['flatplusfree_sort_order'] = $this->config->get('flatplusfree_sort_order');
		}	
	
	
	    	$ch = curl_init();
 			 // Now set some options (most are optional)
 		     // Set URL to download
  			 curl_setopt($ch, CURLOPT_URL,"http://www.ocmodules.com/version/versionflat.xml");
 		    // Include header in result? (0 = yes, 1 = no)
    		 curl_setopt($ch, CURLOPT_HEADER, 0);
     		// Should cURL return or print out the data? (true = return, false = print)
    		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 		    // Timeout in seconds
    		 curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 		    // Download the given URL, and return output
    		$output = curl_exec($ch);
    		// Close the cURL resource, and free system resources
 		    curl_close($ch);
			$analizador=simplexml_load_string($output,null);
						
			$this->data['version']['version']=$analizador->children()->version;
			$this->data['version']['whats_new']=$analizador->children()->whats_new;
					
		foreach($analizador->children()->other_modules as $other_modules){
				
			$this->data['version']['modules'][]=array(
				
					'name'		=>$other_modules->name,
					'version'	=>$other_modules->version,
					'url'		=>$other_modules->url,
					'manual' 	=>$other_modules->manual,
					'price' 	=>$other_modules->price,
					'resume' 	=>$other_modules->resume,
					'id'		=>$other_modules->id
				);
				
			}
			
				
		$this->load->model('localisation/tax_class');
		
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		//$this->load->model('localisation/geo_zone');
		
		//$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
								
		$this->template = 'shipping/flatplusfree.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/flatplusfree')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>