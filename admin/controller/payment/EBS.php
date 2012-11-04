<?php 
class ControllerPaymentEBS extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load->language('payment/EBS');


	$this->data['heading_title'] = $this->language->get('heading_title');
	$this->load->model('setting/setting');
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('EBS', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect(HTTPS_SERVER .'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
	
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['account_id'] = $this->language->get('account_id');
		$this->data['secret_key'] = $this->language->get('secret_key');
		$this->data['mode'] = $this->language->get('mode');

// new code added for zone
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
//end
$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		if (isset($this->request->post['EBS_account_id'])) {
			$this->data['EBS_account_id'] = $this->request->post['EBS_account_id'];
		} else {
			$this->data['EBS_account_id'] = $this->config->get('EBS_account_id');
		}
		if (isset($this->request->post['EBS_secret_key'])) {
			$this->data['EBS_secret_key'] = $this->request->post['EBS_secret_key'];
		} else {
			$this->data['EBS_secret_key'] = $this->config->get('EBS_secret_key');
		}
		if (isset($this->request->post['EBS_test'])) {
			$this->data['EBS_test'] = $this->request->post['EBS_test'];
		} else {
			$this->data['EBS_test'] = $this->config->get('EBS_test');
		}
			if (isset($this->request->post['EBS_status'])) {
			$this->data['EBS_status'] = $this->request->post['EBS_status'];
		} else {
			$this->data['EBS_status'] = $this->config->get('EBS_status');
		}

// newly added code for zone status for guest checkout
		if (isset($this->request->post['EBS_geo_zone_id'])) {
			$this->data['EBS_geo_zone_id'] = $this->request->post['EBS_geo_zone_id'];
		} else {
			$this->data['EBS_geo_zone_id'] = $this->config->get('EBS_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
//end





		
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

	//	$this->data['error_warning'] = @$this->error['warning'];
if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 	
		
  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER .'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => 'https://secure.ebs.in/pg/ma/sale/pay',
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);
		
   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER .'index.php?route=payment/EBS&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = HTTPS_SERVER .'index.php?route=payment/EBS&token=' . $this->session->data['token'];

		$this->data['cancel'] = HTTPS_SERVER .'index.php?route=extension/payment&token=' . $this->session->data['token'];	
		
		
	
				
		//$this->id       = 'content';
		$this->template = 'payment/EBS.tpl';
		$this->layout   = 'common/layout';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
 
			$this->response->setOutput($this->render(TRUE));
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/EBS')){
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>
