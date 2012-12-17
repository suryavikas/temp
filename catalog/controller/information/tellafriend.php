<?php
class ControllerInformationTellafriend extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('information/tellafriend');

    		$this->document->setTitle($this->language->get('heading_title')); 

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('catalog/tellafriend');
			$this->model_catalog_tellafriend->sendMail($this->request->post);
			$this->data['thanks'] = TRUE;
		}

		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		);
		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('information/tellafriend'),
			'text'      => $this->language->get('heading_title'),
			'separator' => $this->language->get('text_separator')
		);
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_tell_friends'] = $this->language->get('text_tell_friends');
		$this->data['text_enter_friend'] = $this->language->get('text_enter_friend');
		$this->data['text_message'] = $this->language->get('text_message');
		$this->data['text_addresses'] = $this->language->get('text_addresses');
		$this->data['text_click'] = $this->language->get('text_click');
    $this->data['text_thanks'] = $this->language->get('text_thanks');
    $this->data['text_captcha'] = $this->language->get('text_captcha');
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_friend'] = $this->language->get('entry_friend');
		$this->data['entry_captcha'] = $this->language->get('entry_captcha');
    $this->data['button_add_friend'] = $this->language->get('button_add_friend');
    $this->data['button_remove'] = $this->language->get('button_remove');
    $this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['continue'] = $this->url->link('common/home');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		if (isset($this->error['friend'])) {
			$this->data['error_friend'] = $this->error['friend'];
		} else {
			$this->data['error_friend'] = '';
		}
 		if (isset($this->error['captcha'])) {
			$this->data['error_captcha'] = $this->error['captcha'];
		} else {
			$this->data['error_captcha'] = '';
		}	 	

		$this->data['action'] = $this->url->link('information/tellafriend');

		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} else {
			$this->data['name'] = '';
		}
		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} else {
			$this->data['email'] = '';
		}
		if (isset($this->request->post['friend'])) {
			$this->data['friend'] = $this->request->post['friend'];
		} else {
			$this->data['friend'] = '';
		}
		if (isset($this->request->post['friends'])) {
			$this->data['friends'] = $this->request->post['friends'];
		} else {
			$this->data['friends'] = array();
		}
		if (isset($this->request->post['captcha'])) {
			$this->data['captcha'] = $this->request->post['captcha'];
		} else {
			$this->data['captcha'] = '';
		}	    	

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/tellafriend.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/information/tellafriend.tpl';
		} else {
			$this->template = 'default/template/information/tellafriend.tpl';
		}
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
		$this->response->setOutput($this->render());		
	}
	
	public function captcha() {
		$this->load->library('captcha');
		
		$captcha = new Captcha();
		
		$this->session->data['captcha'] = $captcha->getCode();
		
		$captcha->showImage();
	}
	
	private function validate() {
    if ((strlen(utf8_decode($this->request->post['name'])) < 3) || (strlen(utf8_decode($this->request->post['name'])) > 32)) {
      $this->error['name'] = $this->language->get('error_name');
		}

		if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}
    if ((strlen(utf8_decode($this->request->post['friend'])) < 10) || (strlen(utf8_decode($this->request->post['friend'])) > 3000)) {
      $this->error['friend'] = $this->language->get('error_friend');
		} 
    if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
      		$this->error['captcha'] = $this->language->get('error_captcha');
    }
    	
		if (!$this->error) {
			return TRUE;		
		} else {
			return FALSE;
		}
	}
}
?>