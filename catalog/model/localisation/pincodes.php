<?php
class ModelLocalisationPincodes extends Model {
	public function getPincodeDetails($pincode) {
		$query = $this->db->query("SELECT pin_code, city_id, city_name, location, Z.zone_id as state_id, Z.name as state, C.country_id, C.name as country, courier_provider FROM " . DB_PREFIX . "zone_city CT
                                            join " . DB_PREFIX . "zone Z on CT.zone_id = Z.zone_id
                                            join " . DB_PREFIX . "country C on C.country_id = Z.country_id
                                            where CT.is_enabled = '1' and C.status =  '1' and Z.status = '1'
                                            and CT.pin_code = '$pincode';");
		
		return $query->row;
	}	
	
	public function isValidPinCode($pincode) {            
            $query = $this->db->query("SELECT count(*) as count FROM " . DB_PREFIX . "zone_city where pin_code='$pincode'");
            return $query->row;
	}
}
?>