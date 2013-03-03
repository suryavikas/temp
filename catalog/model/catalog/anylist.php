<?php
class ModelCatalogAnylist extends Model {
	public function getLayoutRoutes($layout_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
		
		return $query->rows;
	}

	public function getProductsID($filter) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
		
		$data = array();
		$where = array();
		
		
		if (isset($filter['latest']) and (int)$filter['latest']>0) {
			$latest = $this->db->query("select product_id from `".DB_PREFIX."product` order by date_added limit ".((int)$filter['latest']));
			if ($latest->rows) {
			    $l = array();
				foreach($latest->rows as $r) 
                    $l[]=$r['product_id'];

   			    $where[] = " p.product_id in (".implode(",",$l).") ";
            }
		}
		

		if (isset($filter['specials']) and (int)$filter['specials']>0) {
			$specials = $this->db->query("SELECT DISTINCT ps.product_id FROM " . DB_PREFIX . "product_special ps where ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) limit ".$filter['specials']);
			if ($specials->rows) {
			    $l = array();
				foreach($specials->rows as $r) 
                    $l[]=$r['product_id'];

   			    $where[] = " p.product_id in (".implode(",",$l).") ";
            }
		}
        
		if (isset($filter['products']) and $filter['products']) {
			$f = trim(implode(",",array_unique($filter['products'])));
			if ($f)
    			$where[] = " p.product_id in (".$f.")";
		}
		
		if (isset($filter['category']) and $filter['category']) {
			foreach($filter['category'] as $cat) {
                if (is_numeric($cat))
                    $data[] = $cat;
                     
				$a = $this->getChild(array(),$cat);
				if (is_array($a))
					$data = array_merge($data,$a);
    
			}
			if (!is_array($data)) 
				$f = "null";
			else
				$f = trim(implode(",",array_unique($data)));
				
            if ($f)
    			$where[] = " exists(select pc.* from `".DB_PREFIX."product_to_category` pc where p.product_id=pc.product_id and category_id in (".$f."))";
		}

		$sql = "select p.product_id from `".DB_PREFIX."product` p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE ";
		if ($where)
			$sql .= "(" . implode(" OR ",$where) . ") AND ";
			 
		$sql .= "p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";	 
		
	    if (isset($filter['sort']) and $filter['sort']!='') {
	    	$cols = $this->db->query("SELECT COLUMN_NAME FROM `information_schema`.`COLUMNS` WHERE TABLE_NAME='".DB_PREFIX."product' and TABLE_SCHEMA='".DB_DATABASE."' and COLUMN_NAME='".$filter['sort']."'");
	    	if ($cols) {
				$sql .= " ORDER BY ".$filter['sort'];
				if (isset($filter['order']) and trim(strtolower($filter['order']))=='desc') 
					$sql .= ' DESC';
			} else {
				die("AnyList: SORT FIELD ".$filter['sort']." not found in product table. Only fields in product table are allowed.");
			}
	    }
	    
	    
	 	$cache = md5($sql);
		if (!($data = $this->cache->get('product.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache))) {
			$products = $this->db->query($sql);
			if ($products->rows) {
				foreach($products->rows as $r)
					$data[] = $r['product_id'];
			}
			$this->cache->set('product.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache, $data);
		}
		return $data;
	}


	protected function getChild($data,$cat) {
		$tmp = array();
		$child = $this->db->query("select category_id from `".DB_PREFIX."category` where parent_id=$cat" . (($data) ? " and category_id not in (".implode(",",$data).")" : "") );
		if ($child->rows) {
    		foreach($child->rows as $r) {
    			$data[] = $r['category_id'];
    			if ($c = $this->getChild($data,$r['category_id'])) 
                    $tmp=array_merge($tmp,$c);
    		}
        } else {
            return false;
        }
		return array_merge($data,$tmp);
	}
	
}
?>