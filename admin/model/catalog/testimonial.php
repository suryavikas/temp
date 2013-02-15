<?php
class ModelCatalogTestimonial extends Model {

	public function addTestimonial($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "testimonial SET name='".$this->db->escape($data['name'])."', city = '".$this->db->escape($data['city'])."', status = '" . (int)$data['status'] . "',rating = '".(int)$data['rating'] . "',date_added = '" . $this->db->escape($data['date_added']) . "'");

		$testimonial_id = $this->db->getLastId(); 
			
		foreach ($data['testimonial_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "testimonial_description SET testimonial_id = '" . (int)$testimonial_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

	}
	
	public function editTestimonial($testimonial_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "testimonial SET name='".$this->db->escape($data['name'])."', city = '".$this->db->escape($data['city'])."', status = '" . (int)$data['status'] . "',date_added = '".$this->db->escape($data['date_added']). "',rating = '".(int)$data['rating']."' WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");
					
		foreach ($data['testimonial_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "testimonial_description SET testimonial_id = '" . (int)$testimonial_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
	}
	
	public function deleteTestimonial($testimonial_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "testimonial WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");
	}	

	public function getTestimonial($testimonial_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "testimonial WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		
		return $query->row;
	}
		
	public function getTestimonials($data = array()) {
	
		if ($data) {
			if (!isset($data['language_id']))  $data['language_id']=$this->config->get('config_language_id');
			$sql = "SELECT * FROM " . DB_PREFIX . "testimonial t LEFT JOIN " . DB_PREFIX . "testimonial_description td ON (t.testimonial_id = td.testimonial_id) where language_id = " . $data['language_id'];
		
			$sort_data = array(
				'td.description',				
				'td.title',
				't.name',
				't.date_added',
				't.status'
			);		
		
		
		
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY td.description";	
			}
		
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}		

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			//print_r($sql);exit;
			$query = $this->db->query($sql);
			
			
			
			return $query->rows;
		} else {
		
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonial t LEFT JOIN " . DB_PREFIX . "testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY td.title");
	
				$testimonial_data = $query->rows;
			
	
			return $testimonial_data;			
		}
	}
	
	public function getTestimonialDescriptions($testimonial_id) {
		$testimonial_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		foreach ($query->rows as $result) {
			$testimonial_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description']
			);
		}
		
		return $testimonial_description_data;
	}

	public function isTableExists() {
	    if (mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . DB_PREFIX . "testimonial'"))) {
	      return TRUE;
	    } else {
	      return FALSE;
	    }
	}
	
	public function getTotalTestimonials() {
		if ($this->isTableExists() == false)
			return -1;

      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "testimonial");
		
		return $query->row['total'];
	}	

	public function getCurrentDateTime() {
      	$query = $this->db->query("SELECT NOW() AS cdatetime ");
		
		return $query->row['cdatetime'];
	}	



	public function createDatabaseTables() {
		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."testimonial` ( ";
		$sql .= "`testimonial_id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`name` varchar(64) COLLATE utf8_bin NOT NULL, ";
		$sql .= "`city` varchar(64) COLLATE utf8_bin DEFAULT NULL, "; 
		$sql .= "`email` varchar(96) COLLATE utf8_bin DEFAULT NULL, ";
		$sql .= "`status` int(1) NOT NULL DEFAULT '0', ";
		$sql .= "`rating` int(1) NOT NULL DEFAULT '0', ";
		$sql .= "`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$sql .= "PRIMARY KEY (`testimonial_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		$this->db->query($sql);

		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."testimonial_description` ( ";
		$sql .= "`testimonial_id` int(11) NOT NULL, ";
		$sql .= "`language_id` int(11) NOT NULL, ";
		$sql .= "`title` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`description` text COLLATE utf8_unicode_ci NOT NULL, ";
		$sql .= "PRIMARY KEY (`testimonial_id`,`language_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$this->db->query($sql);
	}
	
	
	public function dropDatabaseTables() {
		$sql = "DROP TABLE IF EXISTS `".DB_PREFIX."testimonial`;";
		$this->db->query($sql);
		$sql = "DROP TABLE IF EXISTS `".DB_PREFIX."testimonial_description`;";
		$this->db->query($sql);
	}

}
?>