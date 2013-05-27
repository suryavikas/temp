<?php 
class ModelPaymentCOD extends Model {
        private $DEFAULT_SHIPPING = 'COD';
  	public function getMethod($address, $total) {
		$this->load->language('payment/cod');                             
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('cod_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
	
		if ($this->config->get('cod_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('cod_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		}  else {
			$status = false;
		}
                if($status != false){
                     $productsInCart = $this->cart->getProducts();
                    foreach ($productsInCart as $product) {
                        $pos = stripos($product['payment_options'], $this->DEFAULT_SHIPPING);
                        if ($pos === false) {
                            $status = false;
                            break;
                        } else{
                            continue;
                        }
                    }
                }

		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'cod',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('cod_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>