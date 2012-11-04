<?php
class ControllerPaymentEBS extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');		

		//$payment_address_id = $this->session->data['payment_address_id'];	
		//$payment_address = $this->model_account_address->getAddress($payment_address_id);
		
		
		

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

    		
		$this->data['action'] = 'https://secure.ebs.in/pg/ma/sale/pay/';
		$this->data['account_id'] = $this->config->get('EBS_account_id');
		$this->data['reference_no']= $this->session->data['order_id']; 
		//$this->data['amount']=$this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
$this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$this->data['description']	=   $this->session->data['payment_method']['title'];
		
		$this->data['name']     = $order_info['payment_firstname'].' '.$order_info['payment_lastname'];
		$this->data['address'] 	= $order_info['payment_address_1'].",".$order_info['payment_address_2'];
		$this->data['city']    	= $order_info['payment_city'];
		$this->data['state'] 	= $order_info['payment_zone'];
		$this->data['postal_code']= $order_info['payment_postcode'];
		$this->data['country']   = $order_info['payment_country_id']; //Iso 3 char code is supported
		#$this->data['email']  	= $this->customer->getEmail();
		#$this->data['phone'] 	= $this->customer->getTelephone();

		$this->data['email']  	= $order_info['email'];
		$this->data['phone']    = $order_info['telephone'];

		
		if(isset($this->session->data['shipping_address_id'])){
			$shipping_address_id = $this->session->data['shipping_address_id'];	
			$shipping_address = $this->model_account_address->getAddress($shipping_address_id);
			$this->data['ship_name']	= $shipping_address['firstname'].' '.$shipping_address['lastname'];
			$this->data['ship_address']	= $shipping_address['address_1'].",".$shipping_address['address_2'];
			$this->data['ship_city']  	= $shipping_address['city'];
			$this->data['ship_state']  	= $shipping_address['zone'];
			$this->data['ship_postal_code'] = $shipping_address['postcode'];
			$this->data['ship_country']   	= $shipping_address['country_id'];//Iso 3 char code is supported
			$this->data['ship_phone']   	= $order_info['telephone'];
			
		}
		else{

			$this->data['ship_name']	= $order_info['payment_firstname'].' '.$order_info['payment_lastname'];
			$this->data['ship_address']	= $order_info['payment_address_1'].",".$order_info['payment_address_2'];
			$this->data['ship_city']  	= $order_info['payment_city'];
			$this->data['ship_state']  	= $order_info['payment_zone'];
			$this->data['ship_postal_code'] = $order_info['payment_postcode'];
			$this->data['ship_country']   	= $order_info['payment_country_id'];//Iso 3 char code is supported
			$this->data['ship_phone']   	= $order_info['telephone'];
		}
 
		$this->data['return_url']      	= HTTPS_SERVER . 'index.php?route=common/response&DR={DR}';



		//$this->url->http('common/res').'&amp;DR={DR}';
		
		if($this->config->get('EBS_test') == "on")
			$this->data['mode']    			= 'TEST';
		else
			$this->data['mode']    			= 'LIVE';



		$hash = $this->config->get('EBS_secret_key'). "|" . $this->config->get('EBS_account_id')."|". $this->data['amount'] . "|". $this->data['reference_no'] . "|". html_entity_decode($this->data['return_url'])  . "|". $this->data['mode'];

		$securehash = md5($hash);



		$this->data['secure_hash']      = $securehash;



	
		
		
		$this->id = 'payment';
	if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/EBS.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/EBS.tpl';
        } else {
            $this->template = 'default/template/payment/EBS.tpl';
        }
		
		$this->render();
		
		
		
		
	}
	

}
?>

