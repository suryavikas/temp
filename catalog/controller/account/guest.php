<?php 
class ControllerAccountGuest extends Controller {
	private $error = array();
		
	public function index() {
 
    	$this->language->load('account/guest');

    	$this->document->setTitle($this->language->get('heading_title'));

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),        	
        	'separator' => false
      	); 
	
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/order', '', 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = $this->language->get('heading_title');
		
    	$this->data['entry_email'] = $this->language->get('entry_email');
    	//$this->data['entry_ip'] = $this->language->get('entry_ip');
    	//$this->data['text_ip'] = $this->language->get('text_ip');
    	$this->data['entry_order_number'] = $this->language->get('entry_order_number');
    	$this->data['text_order_number'] = $this->language->get('text_order_number');

		$this->data['button_continue'] = $this->language->get('button_continue');
		
		$this->data['action'] = $this->url->link('account/guest/info', '', 'SSL');
		
		$this->data['continue'] = $this->url->link('account/guest', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/guest_login.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/guest_login.tpl';
		} else {
			$this->template = 'default/template/account/guest_login.tpl';
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
	
	public function info() { 
		if (isset($this->request->post['order_number'])) {
    		$order_id = $this->request->post['order_number'];
		} else {
			$order_id = '0';
		}
		
		if (isset($this->request->post['email'])) {
    		$email = $this->request->post['email'];
		} else {
			$email = '';
		}
		
		if (isset($this->request->post['ip'])) {
    		$ip = $this->request->post['ip'];
		} else {
			$ip = '0';
		}
		
		$this->language->load('account/guest_history');
				
		$this->load->model('account/guest');
		
			
		$order_info = $this->model_account_guest->getOrder($order_id, $email, $ip);
		
		if ($order_info) {
					
			
			$this->document->setTitle($this->language->get('text_order'));
			
			$this->data['breadcrumbs'] = array();
		
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),        	
				'separator' => false
			); 
		
					
      		$this->data['heading_title'] = $this->language->get('text_order');
			
			$this->data['text_order_detail'] = $this->language->get('text_order_detail');
			$this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
    		$this->data['text_order_id'] = $this->language->get('text_order_id');
			$this->data['text_date_added'] = $this->language->get('text_date_added');
      		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
			$this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
      		$this->data['text_payment_method'] = $this->language->get('text_payment_method');
      		$this->data['text_payment_address'] = $this->language->get('text_payment_address');
      		$this->data['text_history'] = $this->language->get('text_history');
			$this->data['text_comment'] = $this->language->get('text_comment');
			$this->data['text_action'] = $this->language->get('text_action');
			$this->data['text_selected'] = $this->language->get('text_selected');
			$this->data['text_reorder'] = $this->language->get('text_reorder');
			$this->data['text_return'] = $this->language->get('text_return');

      		$this->data['column_name'] = $this->language->get('column_name');
      		$this->data['column_model'] = $this->language->get('column_model');
      		$this->data['column_quantity'] = $this->language->get('column_quantity');
      		$this->data['column_price'] = $this->language->get('column_price');
      		$this->data['column_total'] = $this->language->get('column_total');
			$this->data['column_date_added'] = $this->language->get('column_date_added');
      		$this->data['column_status'] = $this->language->get('column_status');
      		$this->data['column_comment'] = $this->language->get('column_comment');
			
      		$this->data['button_continue'] = $this->language->get('button_continue');
		
			$this->data['action'] = $this->url->link('account/order/info', 'order_id=' . $order_id , 'SSL');
			
			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}
			
			$this->data['order_id'] = $order_id ;
			$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
			
			if ($order_info['shipping_address_format']) {
      			$format = $order_info['shipping_address_format'];
    		} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
		
    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);
	
			$replace = array(
	  			'firstname' => $order_info['shipping_firstname'],
	  			'lastname'  => $order_info['shipping_lastname'],
	  			'company'   => $order_info['shipping_company'],
      			'address_1' => $order_info['shipping_address_1'],
      			'address_2' => $order_info['shipping_address_2'],
      			'city'      => $order_info['shipping_city'],
      			'postcode'  => $order_info['shipping_postcode'],
      			'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
      			'country'   => $order_info['shipping_country']  
			);

			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['shipping_method'] = $order_info['shipping_method'];

			if ($order_info['payment_address_format']) {
      			$format = $order_info['payment_address_format'];
    		} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
		
    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);
	
			$replace = array(
	  			'firstname' => $order_info['payment_firstname'],
	  			'lastname'  => $order_info['payment_lastname'],
	  			'company'   => $order_info['payment_company'],
      			'address_1' => $order_info['payment_address_1'],
      			'address_2' => $order_info['payment_address_2'],
      			'city'      => $order_info['payment_city'],
      			'postcode'  => $order_info['payment_postcode'],
      			'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
      			'country'   => $order_info['payment_country']  
			);
			
			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

      		$this->data['payment_method'] = $order_info['payment_method'];
			
			$this->data['products'] = array();
			
			$products = $this->model_account_guest->getOrderProducts($order_id );

      		foreach ($products as $product) {
				$option_data = array();
				
				$options = $this->model_account_guest->getOrderOptions($order_id , $product['order_product_id']);

         		foreach ($options as $option) {
          			if ($option['type'] != 'file') {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (strlen($option['value']) > 20 ? substr($option['value'], 0, 20) . '..' : $option['value']),
						);
					} else {
						$filename = substr($option['value'], 0, strrpos($option['value'], '.'));
						
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (strlen($filename) > 20 ? substr($filename, 0, 20) . '..' : $filename)
						);						
					}
        		}

        		$this->data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
          			'name'             => $product['name'],
          			'model'            => $product['model'],
          			'option'           => $option_data,
          			'quantity'         => $product['quantity'],
          			'price'            => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
					'total'            => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']),
					'selected'         => isset($this->request->post['selected']) && in_array($result['order_product_id'], $this->request->post['selected'])
        		);
      		}

      		$this->data['totals'] = $this->model_account_guest->getOrderTotals($order_id );
			
			$this->data['comment'] = $order_info['comment'];
			
			$this->data['histories'] = array();

			$results = $this->model_account_guest->getOrderHistories($order_id );

      		foreach ($results as $result) {
        		$this->data['histories'][] = array(
          			'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
          			'status'     => $result['status'],
          			'comment'    => nl2br($result['comment'])
        		);
      		}

      		$this->data['continue'] = $this->url->link('account/order', '', 'SSL');
		
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/guest_history.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/account/guest_history.tpl';
			} else {
				$this->template = 'default/template/account/guest_history.tpl';
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
    	} else {
			$this->document->setTitle($this->language->get('text_order'));
			
      		$this->data['heading_title'] = $this->language->get('text_order');

      		$this->data['text_error'] = $this->language->get('text_error');

      		$this->data['button_continue'] = $this->language->get('button_continue');
			
			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),
				'separator' => false
			);
			
												
      		$this->data['continue'] = $this->url->link('account/order', '', 'SSL');
			 			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
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
  	}
	
	private function validate() {
		if (!isset($this->request->post['selected']) || !isset($this->request->post['action']) || !$this->request->post['action']) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}		
	}
}
?>