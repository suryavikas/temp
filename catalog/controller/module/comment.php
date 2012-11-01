<?php
// ------------------------------------------------------
// TweetBook for Opencart v1.5++
// By MarketInSG
// contact@marketinsg.com
// ------------------------------------------------------
class ControllerModuleComment extends Controller {
	private $_name = 'comment';

	protected function index() {
		static $module = 0;
		
		$this->language->load('information/contact');

    	$this->document->setTitle($this->language->get('heading_title'));  
	
		$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_location'] = $this->language->get('text_location');
		$this->data['text_contact'] = $this->language->get('text_contact');
		$this->data['text_address'] = $this->language->get('text_address');
    	$this->data['text_fax'] = $this->language->get('text_fax');

    	$this->data['entry_name'] = $this->language->get('entry_name');
    	$this->data['entry_email'] = $this->language->get('entry_email');
    	$this->data['entry_enquiry'] = $this->language->get('entry_enquiry');

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
		
		if (isset($this->error['enquiry'])) {
			$this->data['error_enquiry'] = $this->error['enquiry'];
		} else {
			$this->data['error_enquiry'] = '';
		}		
		
 		if (isset($this->error['captcha'])) {
			$this->data['error_captcha'] = $this->error['captcha'];
		} else {
			$this->data['error_captcha'] = '';
		}
		
		$this->data['button_continue'] = $this->language->get('button_continue');

		
		
		$this->data['display'] = $this->config->get($this->_name . '_display');
		
		$this->load->model('setting/store');
		
		$this->data['store'] = $this->config->get('config_name');
		
		$this->data['display']  = $this->config->get($this->_name . '_display');
		
		$this->data['facebook_url']  = $this->config->get($this->_name . '_facebook');
        $this->data['twitter_url'] = $this->config->get($this->_name . '_twitter');
		
		$this->data['face']  = $this->config->get($this->_name . '_face');
        $this->data['twit'] = $this->config->get($this->_name . '_twit');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/comment.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/comment.tpl';
		} else {
			$this->template = 'default/template/module/comment.tpl';
		}
		
		$this->render();
	}
	
	function comment_submit(){
	
	      $this->language->load('information/contact');
		  $this->load->model('module/comment');
	
		  $this->document->setTitle($this->language->get('heading_title'));  
				 
			/*if (($this->request->server['REQUEST_METHOD'] == 'POST'))
			 {
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');				
				$mail->setTo($this->config->get('config_email'));
				$mail->setFrom($this->request->post['email']);
				$mail->setSender($this->request->post['name']);
				$mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
				$mail->setText(strip_tags(html_entity_decode($this->request->post['enquiry'], ENT_QUOTES, 'UTF-8')));
				$mail->send();
				$this->redirect($this->url->link('information/contact/success'));
			}*/
				
 				$modelData = array();			
					if (isset($this->request->post['name'])) {
						$modelData['name'] = $this->request->post['name'];
					} 
					
					if (isset($this->request->post['email'])) {
					$modelData['email'] = $this->request->post['email'];
					} 
					
					if (isset($this->request->post['enquiry'])) {
					$modelData['enquiry'] = $this->request->post['enquiry'];
					} 

				$this->model_module_comment->addComment($modelData);
				
				$json = array(
					'success' => $this->language->get('text_message'),
				);
				$this->response->setOutput(json_encode($json));
	}
			
	
	public function comment() {
		$json = array();
		$this->load->model('module/comment');
		if ($comment_info) {
			$this->load->model('module/comment');

			$json = array(
				'name'              => $comment_info['name'],
				'email'             => $comment_info['email'],
				'enquiry'           => $country_info['enquiry'],
			);
		}
		
	}
        
        
	private function validate() {
    	if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
      		$this->error['name'] = $this->language->get('error_name');
    	}

    	if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}

    	if ((utf8_strlen($this->request->post['enquiry']) < 10) || (utf8_strlen($this->request->post['enquiry']) > 3000)) {
      		$this->error['enquiry'] = $this->language->get('error_enquiry');
    	}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}  	  
  	}
}

?>
