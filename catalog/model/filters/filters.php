<?php

class ModelFiltersFilters extends Model {

    public function getPriceRange($categoryId) {
        //Caching enabled
        $priceRangeArray = $this->cache->get('filters.priceRange' . (int) $this->config->get('config_language_id') . '.' . (int) $categoryId);
        if (!$priceRangeArray) {
            $priceRangeArray = array();
            $query = 'SELECT round(min(price)) as min, round(max(price)) as max FROM ' . DB_PREFIX . 'product P
                    join ' . DB_PREFIX . 'product_to_category POC
                    on POC.product_id = P.product_id
                    where POC.category_id = ' . $categoryId . ' AND P.status = 1' ;
            
            $results = $this->db->query($query);
            $priceRange = $results->row;
            if(empty($priceRange['min']) and empty($priceRange['max']) ){
                return null;
            }
            //Rounding number to an increment of 50
            $minValue = floor(intval($priceRange['min']) / 50) * 50;

            $maxValue = ceil(intval($priceRange['max']) / 50) * 50;
            //Deciding on how many price range we should make
            $divisionFactor = 6;
            if ($maxValue <= 250) {
                $divisionFactor = 4;
            } else if ($maxValue <= 100) {
                $divisionFactor = 3;
            }

            $incrementRange = ceil(intval($maxValue / $divisionFactor) / 50) * 50;
//            echo "incrementRange ".$incrementRange;
//            echo "min ".$minValue;
//            echo "max ".$maxValue;
            $j = 0;

            $tempArray = array();
            for ($i = $minValue; $i <= $maxValue; $i = $i + $incrementRange) {
                
                $tempArray['min'] = floor($i);
                $tempArray['max'] = ceil($i + $incrementRange);
                $priceRangeArray[$j] = $tempArray;
                unset($tempArray);
                $j++;
            }
            $this->cache->set('filters.priceRange' . (int) $this->config->get('config_language_id') . '.' . (int) $categoryId, $priceRangeArray);
        }
//        print_r($priceRangeArray);
        return $priceRangeArray;
    }

    public function getProductOptionsInACategory($categoryId) {
        //Caching enabled
        $productOptionArray = $this->cache->get('filters.options' . (int) $this->config->get('config_language_id') . '.' . (int) $categoryId);
        if (!$productOptionArray) {
            $productOptionArray = array();
            $query = 'SELECT PO.option_id, OD.name as parent_option, OVD.option_value_id as child_id, OVD.name as child_name  FROM
                    ' . DB_PREFIX . 'product_option PO
                    join ' . DB_PREFIX . 'option_description OD
                    on PO.option_id = OD.option_id
                    join ' . DB_PREFIX . 'option_value_description OVD
                    on OVD.option_id = PO.option_id
                    join ' . DB_PREFIX . 'product_to_category PTC
                    on PTC.product_id = PO.product_id
                    join ' . DB_PREFIX . 'product P
                    on P.product_id = PO.product_id
                    AND P.status = 1
                    where PTC.category_id =' . $categoryId
                    . ' group by OD.name,OVD.name';
           
            $results = $this->db->query($query);

            $productOptionsData = $results->rows;

            $oldParentName = null;
            $i = -1;
            $j = -1;
            foreach ($productOptionsData as $options) {
                $i++;
                $childOptionsArray = array();
                //Comparing old parent option name
                if ($oldParentName != $options['parent_option']) {
                    $j++;
                    $i = 0;
                    $oldParentName = $options['parent_option'];
                    $productOptionArray[$j]['name'] = $oldParentName;
//                    echo"Setting parent name".$oldParentName;
                }
                $productOptionArray[$j][$i] = array('child_id' => $options['child_id'], 'child_name' => $options['child_name']);
            }
            $this->cache->set('filters.options' . (int) $this->config->get('config_language_id') . '.' . (int) $categoryId, $productOptionArray);
        }
        return $productOptionArray;
    }

    public function getOptionValues($optionId) {

    }

    public function getManufacturersInACategory($categoryId) {
        //Caching enabled
        $manufacturersData = $this->cache->get('filters.manufacturer' . (int) $this->config->get('config_language_id') . '.' . (int) $categoryId);
        $manufacturersData = array();
        if (!$manufacturersData) {
            $manufacturersData = array();
            $query = 'SELECT distinct(M.manufacturer_id), M.name, M.image from ' . DB_PREFIX . 'product P
                    join ' . DB_PREFIX . 'product_to_category PTC
                    on PTC.product_id=P.product_id
                    join ' . DB_PREFIX . 'manufacturer M
                    on M.manufacturer_id = P.manufacturer_id
                    where category_id=' . $categoryId . ' AND P.status = 1';           
            $results = $this->db->query($query);
            $manufacturersData = $results->rows;
            $this->cache->set('filters.manufacturer' . (int) $this->config->get('config_language_id') . '.' . (int) $categoryId, $manufacturersData);
        }
        return $manufacturersData;
    }

    public function getProductsResult($data = array()) {
		
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$cache = md5(http_build_query($data));

		$product_data = $this->cache->get('filters.product' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache);                
		//echo $product_data;
		if (!$product_data) {
			
			$sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

			if (!empty($data['filter_tag'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_tag pt ON (p.product_id = pt.product_id)";
			}

			if (!empty($data['filter_category_id'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
			}
                        
            if (!empty($data['productOption'])) {
				$sql .= " JOIN " . DB_PREFIX . "product_option_value POV ON (p.product_id = POV.product_id)";
                                $sql .= " AND POV.option_value_id in (".$data['productOption'].")";
			}                        
                        if (!empty($data['manufacturerId'])) {
                                $sql .= " AND p.manufacturer_id in (" .$data['manufacturerId']  . ")";
			}
                        if (!empty($data['minPrice']) && !empty($data['maxPrice'])) {
                                $sql .= " AND p.price between " .$data['minPrice']  . " AND ".$data['maxPrice'];
			}
                        if (!empty($data['inStock'])) {
                                $sql .= " AND p.quantity > 1";
			}

			if (!empty($data['saleItems'])) {
				$sql .= " JOIN " . DB_PREFIX . "product_discount PDC ON (p.product_id = PDC.product_id)";
                                $sql .= " AND (PDC.date_end >= current_date or PDC.date_end = '0000-00-00')";
			}
			//Added for filtering out products which are for sale only 
			if (!empty($data['saleItems'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_discount prd ON (prd.product_id = p.product_id)";			
			}
                        
			$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
			

			if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
				$sql .= " AND (";

				if (!empty($data['filter_name'])) {
					$implode = array();

					$words = explode(' ', $data['filter_name']);

					foreach ($words as $word) {
						if (!empty($data['filter_description'])) {
							$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%' OR LCASE(pd.description) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
						} else {
							$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
						}
					}

					if ($implode) {
						$sql .= " " . implode(" OR ", $implode) . "";
					}
				}

				if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
					$sql .= " OR ";
				}

				if (!empty($data['filter_tag'])) {
					$implode = array();

					$words = explode(' ', $data['filter_tag']);

					foreach ($words as $word) {
						$implode[] = "LCASE(pt.tag) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%' AND pt.language_id = '" . (int)$this->config->get('config_language_id') . "'";
					}

					if ($implode) {
						$sql .= " " . implode(" OR ", $implode) . "";
					}
				}

				$sql .= ")";
			}

			if (!empty($data['filter_category_id'])) {
				if (!empty($data['filter_sub_category'])) {
					$implode_data = array();

					$implode_data[] = "p2c.category_id = '" . (int)$data['filter_category_id'] . "'";

					$this->load->model('catalog/category');

					$categories = $this->model_catalog_category->getCategoriesByParentId($data['filter_category_id']);

					foreach ($categories as $category_id) {
						$implode_data[] = "p2c.category_id = '" . (int)$category_id . "'";
					}

					$sql .= " AND (" . implode(' OR ', $implode_data) . ")";
				} else {
					$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				}
			}

			if (!empty($data['filter_manufacturer_id'])) {
				$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
			}

			//Added for filtering out products which are "IN Stock"
			if (!empty($data['inStock'])) {
				$sql .= " AND p.stock_status_id in (" . $this->getInStockItemId(). ")";
			}

			$sql .= " GROUP BY p.product_id";

			$sort_data = array(
				'pd.name',
				'p.model',
				'p.quantity',
				'p.price',
				'rating',
				'p.sort_order',
				'p.date_added'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
					$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
				} else {
					$sql .= " ORDER BY " . $data['sort'];
				}
			} else {
				$sql .= " ORDER BY p.sort_order";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC, LCASE(pd.name) DESC";
			} else {
				$sql .= " ASC, LCASE(pd.name) ASC";
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

			
			$product_data = array();
	
			$query = $this->db->query($sql);

                        $this->load->model('catalog/product');
			foreach ($query->rows as $result) {                           
				$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
			}

			$this->cache->set('product.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache, $product_data);

		}
		
		return $product_data;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

		if (!empty($data['filter_category_id'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";			
		}
		
		if (!empty($data['filter_tag'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_tag pt ON (p.product_id = pt.product_id)";			
		}

		//Added for filtering out products which are for sale only 
		if (!empty($data['saleItems'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_discount prd ON (prd.product_id = p.product_id)";			
		}
					
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";
								
			if (!empty($data['filter_name'])) {
				$implode = array();
				
				$words = explode(' ', $data['filter_name']);
				
				foreach ($words as $word) {
					if (!empty($data['filter_description'])) {
						$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%' OR LCASE(pd.description) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
					} else {
						$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
					}				
				}
				
				if ($implode) {
					$sql .= " " . implode(" OR ", $implode) . "";
				}
			}
			
			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}
			
			if (!empty($data['filter_tag'])) {
				$implode = array();
				
				$words = explode(' ', $data['filter_tag']);
				
				foreach ($words as $word) {
					$implode[] = "LCASE(pt.tag) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%' AND pt.language_id = '" . (int)$this->config->get('config_language_id') . "'";
				}
				
				if ($implode) {
					$sql .= " " . implode(" OR ", $implode) . "";
				}
			}
		
			$sql .= ")";
		}
		
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$implode_data = array();
				
				$implode_data[] = "p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				
				$this->load->model('catalog/category');
				
				$categories = $this->model_catalog_category->getCategoriesByParentId($data['filter_category_id']);
					
				foreach ($categories as $category_id) {
					$implode_data[] = "p2c.category_id = '" . (int)$category_id . "'";
				}
							
				$sql .= " AND (" . implode(' OR ', $implode_data) . ")";			
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}		
		
		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		
		//Added for filtering out products which are "IN Stock"
		if (!empty($data['inStock'])) {
			$sql .= " AND p.stock_status_id in (" . $this->getInStockItemId(). ")";
		}

		//echo "<pre>".$sql."</pre>";

		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	//Getting the stock_status_id for products which are in stock
	public function getInStockItemId() {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}		
		
		$query = $this->db->query("SELECT stock_status_id as statusId from " . DB_PREFIX . "stock_status where name like '%In%Stock%'");
		
		if (isset($query->row['statusId'])) {
			return $query->row['statusId'];
		} else {
			return 7;	
		}
	}	
}

?>
