<?php
class ModelShippingFlatplusfree extends Model {
	function getQuote($address) {
		$this->load->language('shipping/flatplusfree');
		
		//Get all Geo Zones
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE  country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		$status=false;
		$geo_zone_id=false;
		
		if (!$this->config->get('flatplusfree_status')) {
			$status = true;
		}elseif($query->rows){
		
		foreach ($query->rows as $result) {
		//Check Geo zone Status
		if ($this->config->get('flatplusfree_' . $result['geo_zone_id'] . '_status')) {
			
			$geo_zone_free_total=$this->config->get('flatplusfree_' . $result['geo_zone_id'] . '_free_total');
			$geo_zone_id=$result['geo_zone_id'];	
			$status = true;
			break;
		} else { // Geo zone status is false
			$status = false;
					
		}
		}//for each
		
		}

		if ($status){
		if ($this->cart->getSubTotal() > $geo_zone_free_total) {
			$status = false;
		    }
		}
	

		$this->load->model('localisation/zone');
		
		if (isset($this->session->data['zone_id'])) {
				$zone_id = $this->session->data['zone_id'];			
			} else {
				$zone_id = $address['zone_id'];
			}
		
    	$zone_info2 = $this->model_localisation_zone->getZone($zone_id);
		$zone2 = '';
		if ($zone_info2) {
			$zone2 = $zone_info2['name'];
		}
		
		
		$method_data = array();

		if ($status) {
			$quote_data = array();
	
      		$quote_data['flatplusfree'] = array(
        		'code'         => 'flatplusfree.flatplusfree',
        		'title'        => sprintf($this->language->get('text_description'),$this->currency->format($this->config->get('flatplusfree_' . $geo_zone_id . '_free_total')),$zone2),
        		'cost'         => $this->config->get('flatplusfree_' . $geo_zone_id . '_cost'),
        		'tax_class_id' => $this->config->get('flatplusfree_' . $geo_zone_id . '_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($this->config->get('flatplusfree_' . $geo_zone_id . '_cost'), $this->config->get('flatplusfree_' . $geo_zone_id . '_tax_class_id'), $this->config->get('config_tax')))
      		);

      		$method_data = array(
        		'code'       => 'flatplusfree',
        		'title'      => $this->language->get('text_title'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('flatplusfree_' . $geo_zone_id . '_sort_order'),
        		'error'      => false
      		);
		}else{
			if ($geo_zone_id ){
			$quote_data = array();
			
      		$quote_data['flatplusfree'] = array(
        		'code'         => 'flatplusfree.free',
        		'title'        => sprintf($this->language->get('text_description_free'),$this->currency->format($this->config->get('flatplusfree_' . $geo_zone_id . '_free_total')),$zone2),
        		'cost'         => 0.00,
        		'tax_class_id' => 0,
				'text'         => $this->currency->format(0.00)
      		);

      		$method_data = array(
        		'code'       => 'free',
        		'title'      => $this->language->get('text_title'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('flatplusfree_' . $geo_zone_id . '_sort_order'),
        		'error'      => false
      		);
			
			}
		}
	
		return $method_data;
	}
}
?>