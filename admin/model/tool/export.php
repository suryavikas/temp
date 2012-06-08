<?php

static $config = NULL;
static $log = NULL;

// Error Handler
function error_handler_for_export($errno, $errstr, $errfile, $errline) {
	global $config;
	global $log;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$errors = "Notice";
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$errors = "Warning";
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$errors = "Fatal Error";
			break;
		default:
			$errors = "Unknown";
			break;
	}
		
	if (($errors=='Warning') || ($errors=='Unknown')) {
		return true;
	}

	if ($config->get('config_error_display')) {
		echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}
	
	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}


function fatal_error_shutdown_handler_for_export()
{
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		// fatal error
		error_handler_for_export(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	}
}


class ModelToolExport extends Model {


	function clean( &$str, $allowBlanks=FALSE ) {
		$result = "";
		$n = strlen( $str );
		for ($m=0; $m<$n; $m++) {
			$ch = substr( $str, $m, 1 );
			if (($ch==" ") && (!$allowBlanks) || ($ch=="\n") || ($ch=="\r") || ($ch=="\t") || ($ch=="\0") || ($ch=="\x0B")) {
				continue;
			}
			$result .= $ch;
		}
		return $result;
	}


	function import( &$database, $sql ) {
		foreach (explode(";\n", $sql) as $sql) {
			$sql = trim($sql);
			if ($sql) {
				$database->query($sql);
			}
		}
	}


	protected function getDefaultLanguageId( &$database ) {
		$code = $this->config->get('config_language');
		$sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = '$code'";
		$result = $database->query( $sql );
		$languageId = 1;
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$languageId = $row['language_id'];
				break;
			}
		}
		return $languageId;
	}


	protected function getDefaultWeightUnit() {
		$weightUnit = $this->config->get( 'config_weight_class' );
		return $weightUnit;
	}


	protected function getDefaultMeasurementUnit() {
		$measurementUnit = $this->config->get( 'config_length_class' );
		return $measurementUnit;
	}



	function storeManufacturersIntoDatabase( &$database, &$products, &$manufacturerIds ) {
		// find all manufacturers already stored in the database
		$sql = "SELECT `manufacturer_id`, `name` FROM `".DB_PREFIX."manufacturer`;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$manufacturerId = $row['manufacturer_id'];
				$name = $row['name'];
				if (!isset($manufacturerIds[$name])) {
					$manufacturerIds[$name] = $manufacturerId;
				} else if ($manufacturerIds[$name] < $manufacturerId) {
					$manufacturerIds[$name] = $manufacturerId;
				}
			}
		}

		// add newly introduced manufacturers to the database
		$maxManufacturerId=0;
		foreach ($manufacturerIds as $manufacturerId) {
			$maxManufacturerId = max( $maxManufacturerId, $manufacturerId );
		}
		// $sql = "INSERT INTO `".DB_PREFIX."manufacturer` (`manufacturer_id`, `name`, `image`, `sort_order`) VALUES "; 
		$k = strlen( $sql );
		$first = TRUE;
		foreach ($products as $product) {
			$manufacturerName = $product['manufacturer'];
			if ($manufacturerName=="") {
				continue;
			}
			if (!isset($manufacturerIds[$manufacturerName])) {
				$maxManufacturerId += 1;
				$manufacturerId = $maxManufacturerId;
				$manufacturerIds[$manufacturerName] = $manufacturerId;
				// $sql .= ($first) ? "\n" : ",\n";
				// $first = FALSE;
				$sql = "INSERT INTO `".DB_PREFIX."manufacturer` (`manufacturer_id`, `name`, `image`, `sort_order`) VALUES "; 
				$sql .= "($manufacturerId, '".$database->escape($manufacturerName)."', '', 0)";
				$sql .=" ON DUPLICATE KEY UPDATE ";
				$sql .=" name = '".$database->escape($manufacturerName)."', sort_order = 0 ";
				
				$database->query( $sql );				
			}
		}
		// $sql .= ";\n";
		// if (strlen( $sql ) > $k+2) {
			// $database->query( $sql );
		// }
		
		// populate manufacturer_to_store table
		$storeIdsForManufacturers = array();
		foreach ($products as $product) {
			$manufacturerName = $product['manufacturer'];
			if ($manufacturerName=="") {
				continue;
			}
			$manufacturerId = $manufacturerIds[$manufacturerName];
			$storeIds = $product['store_ids'];
			if (!isset($storeIdsForManufacturers[$manufacturerId])) {
				$storeIdsForManufacturers[$manufacturerId] = array();
			}
			foreach ($storeIds as $storeId) {
				if (!in_array($storeId,$storeIdsForManufacturers[$manufacturerId])) {
					$storeIdsForManufacturers[$manufacturerId][] = $storeId;
					$sql2 = " INSERT INTO `".DB_PREFIX."manufacturer_to_store` (`manufacturer_id`,`store_id`) VALUES ($manufacturerId,$storeId)";
					$sql2 .=" ON DUPLICATE KEY UPDATE ";
					$sql2 .=" manufacturer_id= $manufacturerId, store_id= $storeId ";
					$database->query( $sql2 );
				}
			}
		}
		return TRUE;
	}


	function getWeightClassIds( &$database ) {
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);
		
		// find all weight classes already stored in the database
		$weightClassIds = array();
		$sql = "SELECT `weight_class_id`, `unit` FROM `".DB_PREFIX."weight_class_description` WHERE `language_id`=$languageId;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$weightClassId = $row['weight_class_id'];
				$unit = $row['unit'];
				if (!isset($weightClassIds[$unit])) {
					$weightClassIds[$unit] = $weightClassId;
				}
			}
		}

		return $weightClassIds;
	}


	function getLengthClassIds( &$database ) {
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);
		
		// find all length classes already stored in the database
		$lengthClassIds = array();
		$sql = "SELECT `length_class_id`, `unit` FROM `".DB_PREFIX."length_class_description` WHERE `language_id`=$languageId;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$lengthClassId = $row['length_class_id'];
				$unit = $row['unit'];
				if (!isset($lengthClassIds[$unit])) {
					$lengthClassIds[$unit] = $lengthClassId;
				}
			}
		}

		return $lengthClassIds;
	}


	function getLayoutIds( &$database ) {
		$result = $database->query( "SELECT * FROM `".DB_PREFIX."layout`" );
		$layoutIds = array();
		foreach ($result->rows as $row) {
			$layoutIds[$row['name']] = $row['layout_id'];
		}
		return $layoutIds;
	}


	protected function getAvailableStoreIds( &$database ) {
		$sql = "SELECT store_id FROM `".DB_PREFIX."store`;";
		$result = $database->query( $sql );
		$storeIds = array(0);
		foreach ($result->rows as $row) {
			if (!in_array((int)$row['store_id'],$storeIds)) {
				$storeIds[] = (int)$row['store_id'];
			}
		}
		return $storeIds;
	}


	function storeProductsIntoDatabase( &$database, &$products ) 
	{
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);
		
		// start transaction, remove products
		$sql = "START TRANSACTION;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_description` WHERE language_id=$languageId;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_to_category`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_to_store`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."manufacturer_to_store`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."url_alias` WHERE `query` LIKE 'product_id=%';\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_related`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_tag` WHERE language_id=$languageId;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_to_layout`;\n";
		$this->import( $database, $sql );
		
		// get pre-defined layouts
		$layoutIds = $this->getLayoutIds( $database );
		
		// get pre-defined store_ids
		$availableStoreIds = $this->getAvailableStoreIds( $database );
					echo "hi";

		// store or update manufacturers
		$manufacturerIds = array();
		$ok = $this->storeManufacturersIntoDatabase( $database, $products, $manufacturerIds );
		if (!$ok) {
			$database->query( 'ROLLBACK;' );
			return FALSE;
		}
					echo "hji2";

		// get weight classes
		$weightClassIds = $this->getWeightClassIds( $database );
		
		// get length classes
		$lengthClassIds = $this->getLengthClassIds( $database );
		
		// generate and execute SQL for storing the products
		foreach ($products as $product) {
			$productId = $product['product_id'];
			$productName = $database->escape($product['name']);
			$categories = $product['categories'];
			$quantity = $product['quantity'];
			$model = $database->escape($product['model']);
			$manufacturerName = $product['manufacturer'];
			$manufacturerId = ($manufacturerName=="") ? 0 : $manufacturerIds[$manufacturerName];
			$imageName = $product['image'];
			$shipping = $product['shipping'];
			$shipping = ((strtoupper($shipping)=="YES") || (strtoupper($shipping)=="Y") || (strtoupper($shipping)=="TRUE")) ? 1 : 0;
			$price = trim($product['price']);
			$points = $product['points'];
			$dateAdded = $product['date_added'];
			$dateModified = $product['date_modified'];
			$dateAvailable = $product['date_available'];
			$weight = ($product['weight']=="") ? 0 : $product['weight'];
			$unit = $product['unit'];
			$weightClassId = (isset($weightClassIds[$unit])) ? $weightClassIds[$unit] : 0;
			$status = $product['status'];
			$status = ((strtoupper($status)=="TRUE") || (strtoupper($status)=="YES") || (strtoupper($status)=="ENABLED")) ? 1 : 0;
			$taxClassId = $product['tax_class_id'];
			$viewed = $product['viewed'];
			$productDescription = $database->escape($product['description']);
			$stockStatusId = $product['stock_status_id'];
			$meta_description = $database->escape($product['meta_description']);
			$length = $product['length'];
			$width = $product['width'];
			$height = $product['height'];
			$keyword = $database->escape($product['seo_keyword']);
			$lengthUnit = $product['measurement_unit'];
			$lengthClassId = (isset($lengthClassIds[$lengthUnit])) ? $lengthClassIds[$lengthUnit] : 0;
			$sku = $database->escape($product['sku']);
			$upc = $database->escape($product['upc']);
			$location = $database->escape($product['location']);
			$storeIds = $product['store_ids'];
			$layout = $product['layout'];
			$related = $product['related_ids'];
			$tags = array();
			foreach ($product['tags'] as $tag) {
				$tags[] = $database->escape($tag);
			}
			$subtract = $product['subtract'];
			$subtract = ((strtoupper($subtract)=="TRUE") || (strtoupper($subtract)=="YES") || (strtoupper($subtract)=="ENABLED")) ? 1 : 0;
			$minimum = $product['minimum'];
			$meta_keywords = $database->escape($product['meta_keywords']);
			$sort_order = $product['sort_order'];
			
			$sql  = "INSERT INTO `".DB_PREFIX."product` (`product_id`,`quantity`,`sku`,`upc`,`location`,";
			$sql .= "`stock_status_id`,`model`,`manufacturer_id`,`image`,`shipping`,`price`,`points`,`date_added`,`date_modified`,`date_available`,`weight`,`weight_class_id`,`status`,";
			$sql .= "`tax_class_id`,`viewed`,`length`,`width`,`height`,`length_class_id`,`sort_order`,`subtract`,`minimum`) VALUES ";
			$sql .= "($productId,$quantity,'$sku','$upc','$location',";
			$sql .= "$stockStatusId,'$model',$manufacturerId,'$imageName',$shipping,$price,$points,";
			$sql .= ($dateAdded=='NOW()') ? "$dateAdded," : "'$dateAdded',";
			$sql .= ($dateModified=='NOW()') ? "$dateModified," : "'$dateModified',";
			$sql .= ($dateAvailable=='NOW()') ? "$dateAvailable," : "'$dateAvailable',";
			$sql .= "$weight,$weightClassId,$status,";
			$sql .= "$taxClassId,$viewed,$length,$width,$height,'$lengthClassId','$sort_order','$subtract','$minimum')";
			
			$sql .= " ON DUPLICATE KEY UPDATE ";
			 //loginid="SS", password="SSh"
			$sql .= " quantity= $quantity, ";
			$sql .= " sku='$sku', upc='$upc', location='$location',";
			$sql .= " stock_status_id = $stockStatusId, model='$model', manufacturer_id= $manufacturerId, image='$imageName', shipping=$shipping, price=$price, points=$points,";
			//$sql .= ($dateAdded=='NOW()') ? "$dateAdded," : "'$dateAdded',";
			$sql .= " date_modified = '$dateModified', date_available= '$dateAvailable',";
			$sql .= " weight= $weight, weight_class_id= $weightClassId, status= $status,";
			$sql .= " tax_class_id= $taxClassId,viewed = $viewed, length= $length,";
			$sql .= " width= $width, height= $height, length_class_id='$lengthClassId', sort_order='$sort_order', subtract='$subtract', minimum='$minimum';";
		
		
			$sql2  = "INSERT INTO `".DB_PREFIX."product_description` (`product_id`,`language_id`,`name`,`description`,`meta_description`,`meta_keyword`) VALUES ";
			$sql2 .= "($productId,$languageId,'$productName','$productDescription','$meta_description','$meta_keywords')";
			$sql2 .= " ON DUPLICATE KEY UPDATE ";
			$sql2 .= "language_id =$languageId, name ='$productName', description ='$productDescription', meta_description='$meta_description', meta_keyword='$meta_keywords'";


			
			$database->query($sql);
			$database->query($sql2);
			
			if (count($categories) > 0) {
				// $sql = "INSERT INTO `".DB_PREFIX."product_to_category` (`product_id`,`category_id`) VALUES ";
				// $first = TRUE;
				foreach ($categories as $categoryId) {
					// $sql .= ($first) ? "\n" : ",\n";
					// $first = FALSE;
					$sql  = "INSERT INTO `".DB_PREFIX."product_to_category` (`product_id`,`category_id`) VALUES ";
					$sql .= "($productId,$categoryId)";
					$sql .= " ON DUPLICATE KEY UPDATE ";
					$sql .= " category_id = $categoryId";
					$database->query($sql);
				}
				//$sql .= ";";
				//$database->query($sql);
			}
			if ($keyword) {
				$sql4  = " INSERT INTO `".DB_PREFIX."url_alias` (`query`,`keyword`) VALUES ('product_id=$productId','$keyword')";
				$sql4 .= " ON DUPLICATE KEY UPDATE ";
				$sql4 .= " keyword = '$keyword'";
				
				$database->query($sql4);
			}
			
			// echo $sql4;
			// echo "<br>";
			// echo $sql;
			// die();
			
			foreach ($storeIds as $storeId) {
				if (in_array((int)$storeId,$availableStoreIds)) {
					$sql6  = " INSERT INTO `".DB_PREFIX."product_to_store` (`product_id`,`store_id`) VALUES ($productId, $storeId)";
					$sql6 .= " ON DUPLICATE KEY UPDATE ";
					$sql6 .= " store_id = $storeId";
					$database->query($sql6);
				}
			}
			$layouts = array();
			foreach ($layout as $layoutPart) {
				$nextLayout = explode(':',$layoutPart);
				if ($nextLayout===FALSE) {
					$nextLayout = array( 0, $layoutPart );
				} else if (count($nextLayout)==1) {
					$nextLayout = array( 0, $layoutPart );
				}
				if ( (count($nextLayout)==2) && (in_array((int)$nextLayout[0],$availableStoreIds)) && (is_string($nextLayout[1])) ) {
					$storeId = (int)$nextLayout[0];
					$layoutName = $nextLayout[1];
					if (isset($layoutIds[$layoutName])) {
						$layoutId = (int)$layoutIds[$layoutName];
						if (!isset($layouts[$storeId])) {
							$layouts[$storeId] = $layoutId;
						}
					}
				}
			}
			foreach ($layouts as $storeId => $layoutId) {
				$sql7  = " INSERT INTO `".DB_PREFIX."product_to_layout` (`product_id`,`store_id`,`layout_id`) ";
				$sql7 .= " VALUES ($productId,$storeId,$layoutId)";
				$sql7 .= " ON DUPLICATE KEY UPDATE ";
				$sql7 .= " store_id = $storeId, layout_id = $layoutId";			
				$database->query($sql7);
			}
			if (count($related) > 0) {
				// $sql = "INSERT INTO `".DB_PREFIX."product_related` (`product_id`,`related_id`) VALUES ";
				$first = TRUE;
				foreach ($related as $relatedId) {
					// $sql .= ($first) ? "\n" : ",\n";
					// $first = FALSE;	
					$sql  = " INSERT INTO `".DB_PREFIX."product_related` (`product_id`,`related_id`) ";
					$sql .= " VALUES($productId, $relatedId)";
					$sql .= " ON DUPLICATE KEY UPDATE ";
					$sql .= " related_id = $relatedId";			
					$database->query($sql);
				}
				// $sql .= ";";
				// $database->query($sql);
			}
			if (count($tags) > 0) {
				// $sql = "INSERT INTO `".DB_PREFIX."product_tag` (`product_id`,`tag`,`language_id`) VALUES ";
				// $first = TRUE;
				$inserted_tags = array();
				foreach ($tags as $tag) {
					if ($tag == '') {
						continue;
					}
					if (in_array($tag,$inserted_tags)) {
						continue;
					}
					// $sql .= ($first) ? "\n" : ",\n";
					// $first = FALSE;

					$sql  = "INSERT INTO `".DB_PREFIX."product_tag` (`product_id`,`tag`,`language_id`) VALUES ";
					$sql .= "($productId,'".$database->escape($tag)."',$languageId)";
					$sql .= " ON DUPLICATE KEY UPDATE ";
					$sql .= " tag = '".$database->escape($tag)."', language_id = $languageId";
					
					$inserted_tags[] = $tag;
					if (count($inserted_tags)>0) {
						$database->query($sql);
					}
				}
				// $sql .= ";";
				// if (count($inserted_tags)>0) {
					// $database->query($sql);
				// }
			}
		}
		
		// final commit
		$database->query("COMMIT;");
		return TRUE;
	}


	protected function detect_encoding( $str ) {
		// auto detect the character encoding of a string
		return mb_detect_encoding( $str, 'UTF-8,ISO-8859-15,ISO-8859-1,cp1251,KOI8-R' );
	}


	function uploadProducts( &$reader, &$database ) {
		// find the default language id and default units
		$languageId = $this->getDefaultLanguageId($database);
		$defaultWeightUnit = $this->getDefaultWeightUnit();
		$defaultMeasurementUnit = $this->getDefaultMeasurementUnit();
		$defaultStockStatusId = $this->config->get('config_stock_status_id');
		
		$data = $reader->getSheet(1);
		$products = array();
		$product = array();
		$isFirstRow = TRUE;
		$i = 0;
		$k = $data->getHighestRow();
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$productId = trim($this->getCell($data,$i,$j++));
			if ($productId=="") {
				continue;
			}
			$name = $this->getCell($data,$i,$j++);
			$name = htmlentities( $name, ENT_QUOTES, $this->detect_encoding($name) );
			$categories = $this->getCell($data,$i,$j++);
			$sku = $this->getCell($data,$i,$j++,'');
			$upc = $this->getCell($data,$i,$j++,'');
			$location = $this->getCell($data,$i,$j++,'');
			$quantity = $this->getCell($data,$i,$j++,'0');
			$model = $this->getCell($data,$i,$j++,'   ');
			$manufacturer = $this->getCell($data,$i,$j++);
			$imageName = $this->getCell($data,$i,$j++);
			$shipping = $this->getCell($data,$i,$j++,'yes');
			$price = $this->getCell($data,$i,$j++,'0.00');
			$points = $this->getCell($data,$i,$j++,'0');
			$dateAdded = $this->getCell($data,$i,$j++);
			$dateAdded = ((is_string($dateAdded)) && (strlen($dateAdded)>0)) ? $dateAdded : "NOW()";
			$dateModified = $this->getCell($data,$i,$j++);
			$dateModified = ((is_string($dateModified)) && (strlen($dateModified)>0)) ? $dateModified : "NOW()";
			$dateAvailable = $this->getCell($data,$i,$j++);
			$dateAvailable = ((is_string($dateAvailable)) && (strlen($dateAvailable)>0)) ? $dateAvailable : "NOW()";
			$weight = $this->getCell($data,$i,$j++,'0');
			$unit = $this->getCell($data,$i,$j++,$defaultWeightUnit);
			$length = $this->getCell($data,$i,$j++,'0');
			$width = $this->getCell($data,$i,$j++,'0');
			$height = $this->getCell($data,$i,$j++,'0');
			$measurementUnit = $this->getCell($data,$i,$j++,$defaultMeasurementUnit);
			$status = $this->getCell($data,$i,$j++,'true');
			$taxClassId = $this->getCell($data,$i,$j++,'0');
			$viewed = $this->getCell($data,$i,$j++,'0');
			$langId = $this->getCell($data,$i,$j++,'1');
			if ($langId!=$languageId) {
				continue;
			}
			$keyword = $this->getCell($data,$i,$j++);
			$description = $this->getCell($data,$i,$j++);
			$description = htmlentities( $description, ENT_QUOTES, $this->detect_encoding($description) );
			$meta_description = $this->getCell($data,$i,$j++);
			$meta_description = htmlentities( $meta_description, ENT_QUOTES, $this->detect_encoding($meta_description) );
			$meta_keywords = $this->getCell($data,$i,$j++);
			$meta_keywords = htmlentities( $meta_keywords, ENT_QUOTES, $this->detect_encoding($meta_keywords) );
			$additionalImageNames = $this->getCell($data,$i,$j++);
			$stockStatusId = $this->getCell($data,$i,$j++,$defaultStockStatusId);
			$storeIds = $this->getCell($data,$i,$j++);
			$layout = $this->getCell($data,$i,$j++);
			$related = $this->getCell($data,$i,$j++);
			$tags = $this->getCell($data,$i,$j++);
			$sort_order = $this->getCell($data,$i,$j++,'0');
			$subtract = $this->getCell($data,$i,$j++,'true');
			$minimum = $this->getCell($data,$i,$j++,'1');
			$product = array();
			$product['product_id'] = $productId;
			$product['name'] = $name;
			$categories = trim( $this->clean($categories, FALSE) );
			$product['categories'] = ($categories=="") ? array() : explode( ",", $categories );
			if ($product['categories']===FALSE) {
				$product['categories'] = array();
			}
			$product['quantity'] = $quantity;
			$product['model'] = $model;
			$product['manufacturer'] = $manufacturer;
			$product['image'] = $imageName;
			$product['shipping'] = $shipping;
			$product['price'] = $price;
			$product['points'] = $points;
			$product['date_added'] = $dateAdded;
			$product['date_modified'] = $dateModified;
			$product['date_available'] = $dateAvailable;
			$product['weight'] = $weight;
			$product['unit'] = $unit;
			$product['status'] = $status;
			$product['tax_class_id'] = $taxClassId;
			$product['viewed'] = $viewed;
			$product['language_id'] = $languageId;
			$product['description'] = $description;
			$product['stock_status_id'] = $stockStatusId;
			$product['meta_description'] = $meta_description;
			$product['length'] = $length;
			$product['width'] = $width;
			$product['height'] = $height;
			$product['seo_keyword'] = $keyword;
			$product['measurement_unit'] = $measurementUnit;
			$product['sku'] = $sku;
			$product['upc'] = $upc;
			$product['location'] = $location;
			$storeIds = trim( $this->clean($storeIds, FALSE) );
			$product['store_ids'] = ($storeIds=="") ? array() : explode( ",", $storeIds );
			if ($product['store_ids']===FALSE) {
				$product['store_ids'] = array();
			}
			$product['related_ids'] = ($related=="") ? array() : explode( ",", $related );
			if ($product['related_ids']===FALSE) {
				$product['related_ids'] = array();
			}
			$product['layout'] = ($layout=="") ? array() : explode( ",", $layout );
			if ($product['layout']===FALSE) {
				$product['layout'] = array();
			}
			$product['tags'] = ($tags=="") ? array() : explode( ",", $tags );
			if ($product['tags']===FALSE) {
				$product['tags'] = array();
			}
			$product['subtract'] = $subtract;
			$product['minimum'] = $minimum;
			$product['meta_keywords'] = $meta_keywords;
			$product['sort_order'] = $sort_order;
			$products[$productId] = $product;
		}
		return $this->storeProductsIntoDatabase( $database, $products );
	}


	function storeCategoriesIntoDatabase( &$database, &$categories ) 
	{
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);

		// start transaction, remove categories
		$sql = "START TRANSACTION;\n";
		$sql .= "DELETE FROM `".DB_PREFIX."category`;\n";
		$sql .= "DELETE FROM `".DB_PREFIX."category_description` WHERE language_id=$languageId;\n";
		$sql .= "DELETE FROM `".DB_PREFIX."category_to_store`;\n";
		$sql .= "DELETE FROM `".DB_PREFIX."url_alias` WHERE `query` LIKE 'category_id=%';\n";
		$sql .= "DELETE FROM `".DB_PREFIX."category_to_layout`;\n";
		$this->import( $database, $sql );
		
		// get pre-defined layouts
		$layoutIds = $this->getLayoutIds( $database );
		
		// get pre-defined store_ids
		$availableStoreIds = $this->getAvailableStoreIds( $database );
		
		// generate and execute SQL for inserting the categories
		foreach ($categories as $category) {
			$categoryId = $category['category_id'];
			$imageName = $category['image'];
			$parentId = $category['parent_id'];
			$top = $category['top'];
			$top = ((strtoupper($top)=="TRUE") || (strtoupper($top)=="YES") || (strtoupper($top)=="ENABLED")) ? 1 : 0;
			$columns = $category['columns'];
			$sortOrder = $category['sort_order'];
			$dateAdded = $category['date_added'];
			$dateModified = $category['date_modified'];
			$languageId = $category['language_id'];
			$name = $database->escape($category['name']);
			$description = $database->escape($category['description']);
			$meta_description = $database->escape($category['meta_description']);
			$meta_keywords = $database->escape($category['meta_keywords']);
			$keyword = $database->escape($category['seo_keyword']);
			$storeIds = $category['store_ids'];
			$layout = $category['layout'];
			$status = $category['status'];
			$status = ((strtoupper($status)=="TRUE") || (strtoupper($status)=="YES") || (strtoupper($status)=="ENABLED")) ? 1 : 0;
			
			$sql2 = "INSERT INTO `".DB_PREFIX."category` (`category_id`, `image`, `parent_id`, `top`, `column`, `sort_order`, `date_added`, `date_modified`, `status`)";
			$sql2 .= " VALUES( $categoryId, '$imageName', $parentId, $top, $columns, $sortOrder, ";
			$sql2 .= ($dateAdded=='NOW()') ? "$dateAdded," : "'$dateAdded',";
			$sql2 .= ($dateModified=='NOW()') ? "$dateModified," : "'$dateModified',";
			$sql2 .= " $status)";
			$sql2 .= " ON DUPLICATE KEY UPDATE ";
			$sql2 .= " image = '$imageName', parent_id = $parentId,";
			$sql2 .= " top= $top, `column`= $columns, sort_order= $sortOrder, date_added= '$dateAdded', date_modified= '$dateModified'";
			$database->query( $sql2 );
			
			unset($sql2);
			
			$sql3  = "INSERT INTO `".DB_PREFIX."category_description` (`category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) ";
			$sql3 .= " VALUES( $categoryId, $languageId, '$name', '$description', '$meta_description', '$meta_keywords')";
			$sql3 .= " ON DUPLICATE KEY UPDATE ";
			$sql3 .= " language_id= $languageId, name= '$name', description= '$description', meta_description= '$meta_description', meta_keyword= '$meta_keywords'";
			$database->query( $sql3 );
			
			if ($keyword) {
				$sql5  = " INSERT INTO `".DB_PREFIX."url_alias` (`query`,`keyword`) VALUES ('category_id=$categoryId','$keyword')";
				$sql5 .= " ON DUPLICATE KEY UPDATE ";
				$sql5 .= " keyword = '$keyword'";
				$database->query($sql5);
			}
			foreach ($storeIds as $storeId) {
				if (in_array((int)$storeId,$availableStoreIds)) {
					$sql6  = "INSERT INTO `".DB_PREFIX."category_to_store` (`category_id`,`store_id`) VALUES ($categoryId,$storeId)";
					$sql6 .= " ON DUPLICATE KEY UPDATE ";
					$sql6 .= " store_id = $storeId";					
					$database->query($sql6);
				}
			}
			$layouts = array();
			foreach ($layout as $layoutPart) {
				$nextLayout = explode(':',$layoutPart);
				if ($nextLayout===FALSE) {
					$nextLayout = array( 0, $layoutPart );
				} else if (count($nextLayout)==1) {
					$nextLayout = array( 0, $layoutPart );
				}
				if ( (count($nextLayout)==2) && (in_array((int)$nextLayout[0],$availableStoreIds)) && (is_string($nextLayout[1])) ) {
					$storeId = (int)$nextLayout[0];
					$layoutName = $nextLayout[1];
					if (isset($layoutIds[$layoutName])) {
						$layoutId = (int)$layoutIds[$layoutName];
						if (!isset($layouts[$storeId])) {
							$layouts[$storeId] = $layoutId;
						}
					}
				}
			}
			foreach ($layouts as $storeId => $layoutId) {
				$sql7  = " INSERT INTO `".DB_PREFIX."category_to_layout` (`category_id`,`store_id`,`layout_id`)";
				$sql7 .= " VALUES ($categoryId, $storeId, $layoutId)";
				$sql7 .= " ON DUPLICATE KEY UPDATE ";
				$sql7 .= " store_id = $storeId, layout_id = $layoutId";
				$database->query($sql7);
			}
		}
		
		// final commit
		$database->query( "COMMIT;" );
		return TRUE;
	}


	function uploadCategories( &$reader, &$database ) 
	{
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);
		
		$data = $reader->getSheet(0);
		$categories = array();
		$isFirstRow = TRUE;
		$i = 0;
		$k = $data->getHighestRow();
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$categoryId = trim($this->getCell($data,$i,$j++));
			if ($categoryId=="") {
				continue;
			}
			$parentId = $this->getCell($data,$i,$j++,'0');
			$name = $this->getCell($data,$i,$j++);
			$name = htmlentities( $name, ENT_QUOTES, $this->detect_encoding($name) );
			$top = $this->getCell($data,$i,$j++,($parentId=='0')?'true':'false');
			$columns = $this->getCell($data,$i,$j++,($parentId=='0')?'1':'0');
			$sortOrder = $this->getCell($data,$i,$j++,'0');
			$imageName = trim($this->getCell($data,$i,$j++));
			$dateAdded = trim($this->getCell($data,$i,$j++));
			$dateAdded = ((is_string($dateAdded)) && (strlen($dateAdded)>0)) ? $dateAdded : "NOW()";
			$dateModified = trim($this->getCell($data,$i,$j++));
			$dateModified = ((is_string($dateModified)) && (strlen($dateModified)>0)) ? $dateModified : "NOW()";
			$langId = $this->getCell($data,$i,$j++,'1');
			if ($langId != $languageId) {
				continue;
			}
			$keyword = $this->getCell($data,$i,$j++);
			$description = $this->getCell($data,$i,$j++);
			$description = htmlentities( $description, ENT_QUOTES, $this->detect_encoding($description) );
			$meta_description = $this->getCell($data,$i,$j++);
			$meta_description = htmlentities( $meta_description, ENT_QUOTES, $this->detect_encoding($meta_description) );
			$meta_keywords = $this->getCell($data,$i,$j++);
			$meta_keywords = htmlentities( $meta_keywords, ENT_QUOTES, $this->detect_encoding($meta_keywords) );
			$storeIds = $this->getCell($data,$i,$j++);
			$layout = $this->getCell($data,$i,$j++,'');
			$status = $this->getCell($data,$i,$j++,'true');
			$category = array();
			$category['category_id'] = $categoryId;
			$category['image'] = $imageName;
			$category['parent_id'] = $parentId;
			$category['sort_order'] = $sortOrder;
			$category['date_added'] = $dateAdded;
			$category['date_modified'] = $dateModified;
			$category['language_id'] = $languageId;
			$category['name'] = $name;
			$category['top'] = $top;
			$category['columns'] = $columns;
			$category['description'] = $description;
			$category['meta_description'] = $meta_description;
			$category['meta_keywords'] = $meta_keywords;
			$category['seo_keyword'] = $keyword;
			$storeIds = trim( $this->clean($storeIds, FALSE) );
			$category['store_ids'] = ($storeIds=="") ? array() : explode( ",", $storeIds );
			if ($category['store_ids']===FALSE) {
				$category['store_ids'] = array();
			}
			$category['layout'] = ($layout=="") ? array() : explode( ",", $layout );
			if ($category['layout']===FALSE) {
				$category['layout'] = array();
			}
			$category['status'] = $status;
			$categories[$categoryId] = $category;
		}
		return $this->storeCategoriesIntoDatabase( $database, $categories );
	}


	function storeOptionsIntoDatabase( &$database, &$options ) 
	{
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);
		
		// reuse old option_ids and option_value_ids where possible
		$oldOptionIds = array();       // indexed by [name][type]
		$oldOptionValueIds = array();  // indexed by [name][type][value]
		$maxOptionId = 0;
		$maxOptionValueId = 0;
		$sql  = "SELECT o.*, od.name, ovd.option_value_id, ovd.name AS value FROM `".DB_PREFIX."option` o ";
		$sql .= "INNER JOIN `".DB_PREFIX."option_description` od ON od.option_id=o.option_id AND od.language_id=$languageId ";
		$sql .= "LEFT JOIN  `".DB_PREFIX."option_value_description` ovd ON ovd.option_id=o.option_id AND ovd.language_id=$languageId ";
		$result = $database->query( $sql );
		foreach ($result->rows as $row) {
			$name = $row['name'];
			$type = $row['type'];
			$value = $row['value'];
			$optionId = $row['option_id'];
			$optionValueId = $row['option_value_id'];
			if ($maxOptionId < $optionId) {
				$maxOptionId = $optionId;
			}
			if ($maxOptionValueId < $optionValueId) {
				$maxOptionValueId = $optionValueId;
			}
			if (!isset($oldOptionIds[$name])) {
				$oldOptionIds[$name] = array();
			}
			if (!isset($oldOptionIds[$name][$type])) {
				$oldOptionIds[$name][$type] = $optionId;
			}
			if (!isset($oldOptionValueIds[$name])) {
				$oldOptionValueIds[$name] = array();
			}
			if (!isset($oldOptionValueIds[$name][$type])) {
				$oldOptionValueIds[$name][$type] = array();
			}
			if (!isset($oldOptionValueIds[$name][$type][$value])) {
				$oldOptionValueIds[$name][$type][$value] = $optionValueId;
			}
		}
		
		// start transaction, remove options
		$sql = "START TRANSACTION;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."option`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."option_description` WHERE language_id=$languageId;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."option_value`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."option_value_description` WHERE language_id=$languageId;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_option`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_option_value`;\n";
		$this->import( $database, $sql );
		
		$newOptionIds = array();       // indexed by [name][type]
		$newOptionValueIds = array();  // indexed by [name[[type][value]
		$productOptionIds = array();   // indexed by [product_id][option_id]
		$maxProductOptionId = 0;
		$maxProductOptionValueId = 0;
		$countOptions = 0;
		
		foreach ($options as $option) {
			$productId = $option['product_id'];
			$langId = $option['language_id'];
			$name = $option['option'];
			$type = $option['type'];
			$value = $option['value'];
			$required = $option['required'];
			$required = ((strtoupper($required)=="TRUE") || (strtoupper($required)=="YES") || (strtoupper($required)=="ENABLED")) ? 1 : 0;
			if (!isset($newOptionIds[$name])) {
				$newOptionIds[$name] = array();
			}
			if (!isset($newOptionIds[$name][$type])) {
				if (isset($oldOptionIds[$name][$type])) {
					$optionId = $oldOptionIds[$name][$type];
				} else {
					$maxOptionId += 1;
					$optionId = $maxOptionId;
				}
				$newOptionIds[$name][$type] = $optionId;
				$sql  = "INSERT INTO `".DB_PREFIX."option` (`option_id`,`type`,`sort_order`) VALUES ($optionId,'$type',$countOptions)";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " type= '$type', sort_order= $countOptions";
				$database->query( $sql );
				
				$sql  = " INSERT INTO `".DB_PREFIX."option_description` (`option_id`,`language_id`,`name`)";
				$sql .= " VALUES ($optionId,$langId,'".$database->escape($name)."')";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " language_id= $langId, name='".$database->escape($name)."'";
				$database->query( $sql);
				
				$countOptions += 1;
			}
			if (($type=='select') || ($type=='checkbox') || ($type=='radio') || ($type=='image')) {
				if (!isset($newOptionValueIds[$name])) {
					$newOptionValueIds[$name] = array();
				}
				if (!isset($newOptionValueIds[$name][$type])) {
					$newOptionValueIds[$name][$type] = array();
				}
				if (!isset($newOptionValueIds[$name][$type][$value])) {
					if (isset($oldOptionValueIds[$name][$type][$value])) {
						$optionValueId = $oldOptionValueIds[$name][$type][$value];
					} else {
						$maxOptionValueId += 1;
						$optionValueId = $maxOptionValueId;
					}
					$newOptionValueIds[$name][$type][$value] = $optionValueId;
					$sortOrder = ($option['sort_order'] == '') ? 0 : $option['sort_order'];
					$image = ($option['image'] == '') ? '' : $this->db->escape($option['image']);
					$optionId = $newOptionIds[$name][$type];
					
					$sql  = "INSERT INTO `".DB_PREFIX."option_value` (`option_value_id`,`option_id`,`image`,`sort_order`) VALUES ";
					$sql .= "($optionValueId,$optionId,'$image',$sortOrder)";
					$sql .= " ON DUPLICATE KEY UPDATE ";
					$sql .= " option_id= $optionId, image='$image', sort_order= $sortOrder";
					$database->query( $sql );
					
					$sql  = "INSERT INTO `".DB_PREFIX."option_value_description` (`option_value_id`,`language_id`,`option_id`,`name`) VALUES ";
					$sql .= "($optionValueId,$langId,$optionId,'".$database->escape($value)."')";
					$sql .= " ON DUPLICATE KEY UPDATE ";
					$sql .= " language_id=$langId, option_id= $optionId, name='".$database->escape($value)."'";					
					
					$database->query( $sql );
				}
			}
			if (!isset($productOptionIds[$productId])) {
				$productOptionIds[$productId] = array();
			}
			$optionId = $newOptionIds[$name][$type];
			if (!isset($productOptionIds[$productId][$optionId])) {
				$maxProductOptionId += 1;
				$productOptionId = $maxProductOptionId;
				$productOptionIds[$productId][$optionId] = $productOptionId;
				if (($type!='select') && ($type!='checkbox') && ($type!='radio') && ($type!='image')) {
					$productOptionValue = $value;
				} else {
					$productOptionValue = '';
				}
				$sql  = "INSERT INTO `".DB_PREFIX."product_option` (`product_option_id`,`product_id`,`option_id`,`option_value`,`required`) VALUES ";
				$sql .= "($productOptionId,$productId,$optionId,'".$database->escape($productOptionValue)."',$required)";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " product_id= $productId, option_id= $optionId, option_value= '".$database->escape($productOptionValue)."', required= $required";
				
				$database->query( $sql );
			}
			if (($type=='select') || ($type=='checkbox') || ($type=='radio') || ($type=='image')) {
				$quantity = $option['quantity'];
				$subtract = $option['subtract'];
				$subtract = ((strtoupper($subtract)=="TRUE") || (strtoupper($subtract)=="YES") || (strtoupper($subtract)=="ENABLED")) ? 1 : 0;
				$price = $option['price'];
				$pricePrefix = $option['price_prefix'];
				$points = $option['points'];
				$pointsPrefix = $option['points_prefix'];
				$weight = $option['weight'];
				$weightPrefix = $option['weight_prefix'];
				$sortOrder= $option['sort_order'];
				$maxProductOptionValueId += 1;
				$productOptionValueId = $maxProductOptionValueId;
				$optionId = $newOptionIds[$name][$type];
				$optionValueId = $newOptionValueIds[$name][$type][$value];
				$productOptionId = $productOptionIds[$productId][$optionId];
				
				$sql  = "INSERT INTO `".DB_PREFIX."product_option_value` ";
				$sql .= " (`product_option_value_id`,`product_option_id`,`product_id`,`option_id`,`option_value_id`,`quantity`,";
				$sql .= " `subtract`,`price`,`price_prefix`,`points`,`points_prefix`,`weight`,`weight_prefix`) ";
				$sql .= " VALUES($productOptionValueId,$productOptionId,$productId,$optionId,$optionValueId,";
				$sql .= " $quantity,$subtract,$price,'$pricePrefix',$points,'$pointsPrefix',$weight,'$weightPrefix') ";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " product_option_id=$productOptionId,product_id=$productId,option_id=$optionId,option_value_id=$optionValueId,quantity=$quantity,";
				$sql .= " subtract=$subtract, price=$price, price_prefix= '$pricePrefix', points=$points, points_prefix='$pointsPrefix',";
				$sql .= " weight=$weight, weight_prefix='$weightPrefix'";

				$database->query( $sql );
			}
		}
		
		$database->query("COMMIT;");
		return TRUE;
	}



	function uploadOptions( &$reader, &$database ) 
	{
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);
		
		$data = $reader->getSheet(2);
		$options = array();
		$i = 0;
		$k = $data->getHighestRow();
		$isFirstRow = TRUE;
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$productId = trim($this->getCell($data,$i,$j++));
			if ($productId=="") {
				continue;
			}
			$langId = $this->getCell($data,$i,$j++);
			if ($langId!=$languageId) {
				continue;
			}
			$option = $this->getCell($data,$i,$j++);
			$type = $this->getCell($data,$i,$j++,'select');
			$value = $this->getCell($data,$i,$j++,'');
			$image = $this->getCell($data,$i,$j++,'');
			$required = $this->getCell($data,$i,$j++,'true');
			$quantity = $this->getCell($data,$i,$j++,'0');
			$subtract = $this->getCell($data,$i,$j++,'false');
			$price = $this->getCell($data,$i,$j++,'0');
			$pricePrefix = $this->getCell($data,$i,$j++,'+');
			$points = $this->getCell($data,$i,$j++,'0');
			$pointsPrefix = $this->getCell($data,$i,$j++,'+');
			$weight = $this->getCell($data,$i,$j++,'0.00');
			$weightPrefix = $this->getCell($data,$i,$j++,'+');
			$sortOrder = $this->getCell($data,$i,$j++,'0');
			$options[$i] = array();
			$options[$i]['product_id'] = $productId;
			$options[$i]['language_id'] = $languageId;
			$options[$i]['option'] = $option;
			$options[$i]['type'] = $type;
			$options[$i]['value'] = $value;
			$options[$i]['image'] = $image;
			$options[$i]['required'] = $required;
			if (($type=='select') || ($type=='checkbox') || ($type=='radio') || ($type=='image')) {
				$options[$i]['quantity'] = $quantity;
				$options[$i]['subtract'] = $subtract;
				$options[$i]['price'] = $price;
				$options[$i]['price_prefix'] = $pricePrefix;
				$options[$i]['points'] = $points;
				$options[$i]['points_prefix'] = $pointsPrefix;
				$options[$i]['weight'] = $weight;
				$options[$i]['weight_prefix'] = $weightPrefix;
				$options[$i]['sort_order'] = $sortOrder;
			}
		}
		return $this->storeOptionsIntoDatabase( $database, $options );
	}


	function storeAttributesIntoDatabase( &$database, &$attributes ) {
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);
		
		// reuse old attribute_group_ids and attribute_ids where possible
		$oldAttributeGroupIds = array();    // indexed by [group]
		$oldAttributeIds = array();         // indexed by [group][name]
		$maxAttributeGroupId = 0;
		$maxAttributeId = 0;
		$sql  = "SELECT ag.attribute_group_id, agd.name AS `group`, ad.attribute_id, ad.name FROM `".DB_PREFIX."attribute_group` ag ";
		$sql .= "INNER JOIN `".DB_PREFIX."attribute_group_description` agd ON agd.attribute_group_id=ag.attribute_group_id AND agd.language_id=$languageId ";
		$sql .= "LEFT JOIN  `".DB_PREFIX."attribute` a ON a.attribute_group_id=ag.attribute_group_id ";
		$sql .= "INNER JOIN  `".DB_PREFIX."attribute_description` ad ON ad.attribute_id=a.attribute_id AND ad.language_id=$languageId ";
		$result = $database->query( $sql );
		foreach ($result->rows as $row) {
			$attributeGroupId = $row['attribute_group_id'];
			$attributeId = $row['attribute_id'];
			$group = $row['group'];
			$name = $row['name'];
			if ($maxAttributeGroupId < $attributeGroupId) {
				$maxAttributeGroupId = $attributeGroupId;
			}
			if ($maxAttributeId < $attributeId) {
				$maxAttributeId = $attributeId;
			}
			if (!isset($oldAttributeGroupIds[$group])) {
				$oldAttributeGroupIds[$group] = $attributeGroupId;
			}
			if (!isset($oldAttributeIds[$group])) {
				$oldAttributeIds[$group] = array();
			}
			if (!isset($oldAttributeIds[$group][$name])) {
				$oldAttributeIds[$group][$name] = $attributeId;
			}
		}
		
		// start transaction, remove attributes
		$sql = "START TRANSACTION;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."attribute_group`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."attribute_group_description` WHERE language_id=$languageId;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."attribute`;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."attribute_description` WHERE language_id=$languageId;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_attribute` WHERE language_id=$languageId;\n";
		$this->import( $database, $sql );
		
		$newAttributeGroupIds = array();  // indexed by [group]
		$newAttributeIds = array();       // indexed by [group[[name]
		foreach ($attributes as $attribute) {
			$productId = $attribute['product_id'];
			$langId = $attribute['language_id'];
			$group = $attribute['group'];
			$name = $attribute['name'];
			$text = $attribute['text'];
			$sortOrder = $attribute['sort_order'];
			if (!isset($newAttributeGroupIds[$group])) {
				if (isset($oldAttributeGroupIds[$group])) {
					$attributeGroupId = $oldAttributeGroupIds[$group];
				} else {
					$maxAttributeGroupId += 1;
					$attributeGroupId = $maxAttributeGroupId;
				}
				$newAttributeGroupIds[$group] = $attributeGroupId;
				$sql  = "INSERT INTO `".DB_PREFIX."attribute_group` (`attribute_group_id`,`sort_order`) VALUES ";
				$sql .= "($attributeGroupId,".count($newAttributeGroupIds).")";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " sort_order=".count($newAttributeGroupIds);					
				$database->query( $sql );
				
				$sql  = "INSERT INTO `".DB_PREFIX."attribute_group_description` (`attribute_group_id`,`language_id`,`name`) VALUES ";
				$sql .= "($attributeGroupId,$langId,'".$database->escape($group)."')";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " language_id=$langId, name='".$database->escape($group)."'";
				$database->query( $sql );
			}
			if (!isset($newAttributeIds[$group])) {
				$newAttributeIds[$group] = array();
			}
			if (!isset($newAttributeIds[$group][$name])) {
				if (isset($oldAttributeIds[$group][$name])) {
					$attributeId = $oldAttributeIds[$group][$name];
				} else {
					$maxAttributeId += 1;
					$attributeId = $maxAttributeId;
				}
				$newAttributeIds[$group][$name] = $attributeId;
				$attributeGroupId = $newAttributeGroupIds[$group];
				$sql  = "INSERT INTO `".DB_PREFIX."attribute` (`attribute_id`,`attribute_group_id`,`sort_order`) VALUES ";
				$sql .= "($attributeId,$attributeGroupId,".count($newAttributeIds[$group]).")";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " attribute_group_id=$attributeGroupId, sort_order=".count($newAttributeIds[$group]);
				$database->query( $sql );

				$sql  = "INSERT INTO `".DB_PREFIX."attribute_description` (`attribute_id`,`language_id`,`name`) VALUES ";
				$sql .= "($attributeId,$langId,'".$database->escape($name)."')";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " language_id=$langId, name='".$database->escape($name)."'";				
				$database->query( $sql );
			}
			$attributeId = $newAttributeIds[$group][$name];
			$sql  = "INSERT INTO `".DB_PREFIX."product_attribute` (`product_id`,`attribute_id`,`language_id`,`text`) VALUES ";
			$sql .= "($productId,$attributeId,$langId,'".$database->escape( $text )."')";
			$sql .= " ON DUPLICATE KEY UPDATE ";
			$sql .= " attribute_id=$attributeId, language_id=$langId, text='".$database->escape($text)."'";				
			$database->query( $sql );
		}
		
		$database->query("COMMIT;");
		return TRUE;
	}


	function uploadAttributes( &$reader, &$database ) 
	{
		// find the default language id
		$languageId = $this->getDefaultLanguageId($database);
		
		$data = $reader->getSheet(3);
		$attributes = array();
		$i = 0;
		$k = $data->getHighestRow();
		$isFirstRow = TRUE;
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$productId = trim($this->getCell($data,$i,$j++));
			if ($productId=="") {
				continue;
			}
			$langId = $this->getCell($data,$i,$j++);
			if ($langId!=$languageId) {
				continue;
			}
			$group = trim($this->getCell($data,$i,$j++));
			if ($group=='') {
				continue;
			}
			$name = trim($this->getCell($data,$i,$j++));
			if ($name=='') {
				continue;
			}
			$text = $this->getCell($data,$i,$j++);
			$sortOrder = $this->getCell($data,$i,$j++);
			$attributes[$i] = array();
			$attributes[$i]['product_id'] = $productId;
			$attributes[$i]['language_id'] = $languageId;
			$attributes[$i]['group'] = $group;
			$attributes[$i]['name'] = $name;
			$attributes[$i]['text'] = $text;
			$attributes[$i]['sort_order'] = $sortOrder;
		}
		return $this->storeAttributesIntoDatabase( $database, $attributes );
	}


	function storeSpecialsIntoDatabase( &$database, &$specials )
	{
		$sql = "START TRANSACTION;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_special`;\n";
		$this->import( $database, $sql );

		// find existing customer groups from the database
		$sql = "SELECT * FROM `".DB_PREFIX."customer_group`";
		$result = $database->query( $sql );
		$maxCustomerGroupId = 0;
		$customerGroups = array();
		foreach ($result->rows as $row) {
			$customerGroupId = $row['customer_group_id'];
			$name = $row['name'];
			if (!isset($customerGroups[$name])) {
				$customerGroups[$name] = $customerGroupId;
			}
			if ($maxCustomerGroupId < $customerGroupId) {
				$maxCustomerGroupId = $customerGroupId;
			}
		}

		// add additional customer groups into the database
		foreach ($specials as $special) {
			$name = $special['customer_group'];
			if (!isset($customerGroups[$name])) {
				$maxCustomerGroupId += 1;
				$sql  = "INSERT INTO `".DB_PREFIX."customer_group` (`customer_group_id`, `name`) VALUES "; 
				$sql .= "($maxCustomerGroupId, '$name')";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " name='$name'";				
				//$sql .= ";\n";
				$database->query($sql);
				$customerGroups[$name] = $maxCustomerGroupId;
			}
		}

		// store product specials into the database
		$productSpecialId = 0;
		//$first = TRUE;

		foreach ($specials as $special) {
			$productSpecialId += 1;
			$productId = $special['product_id'];
			$name = $special['customer_group'];
			$customerGroupId = $customerGroups[$name];
			$priority = $special['priority'];
			$price = $special['price'];
			$dateStart = $special['date_start'];
			$dateEnd = $special['date_end'];
			//$sql .= ($first) ? "\n" : ",\n";
			//$first = FALSE;
			$sql = "INSERT INTO `".DB_PREFIX."product_special`(`product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end` ) VALUES "; 
			$sql .= "($productSpecialId,$productId,$customerGroupId,$priority,$price,'$dateStart','$dateEnd')";
			$sql .= " ON DUPLICATE KEY UPDATE ";
			$sql .= " product_id=$productId, customer_group_id=$customerGroupId, priority=$priority,";
			$sql .= " price=$price, date_start='$dateStart', date_end='$dateEnd'";	

			$database->query($sql);

		}
		// if (!$first) {
			// $database->query($sql);
		// }

		$database->query("COMMIT;");
		return TRUE;
	}


	function uploadSpecials( &$reader, &$database ) 
	{
		$data = $reader->getSheet(4);
		$specials = array();
		$i = 0;
		$k = $data->getHighestRow();
		$isFirstRow = TRUE;
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$productId = trim($this->getCell($data,$i,$j++));
			if ($productId=="") {
				continue;
			}
			$customerGroup = trim($this->getCell($data,$i,$j++));
			if ($customerGroup=="") {
				continue;
			}
			$priority = $this->getCell($data,$i,$j++,'0');
			$price = $this->getCell($data,$i,$j++,'0');
			$dateStart = $this->getCell($data,$i,$j++,'0000-00-00');
			$dateEnd = $this->getCell($data,$i,$j++,'0000-00-00');
			$specials[$i] = array();
			$specials[$i]['product_id'] = $productId;
			$specials[$i]['customer_group'] = $customerGroup;
			$specials[$i]['priority'] = $priority;
			$specials[$i]['price'] = $price;
			$specials[$i]['date_start'] = $dateStart;
			$specials[$i]['date_end'] = $dateEnd;
		}
		return $this->storeSpecialsIntoDatabase( $database, $specials );
	}


	function storeDiscountsIntoDatabase( &$database, &$discounts )
	{
		$sql = "START TRANSACTION;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_discount`;\n";
		$this->import( $database, $sql );

		// find existing customer groups from the database
		$sql = "SELECT * FROM `".DB_PREFIX."customer_group`";
		$result = $database->query( $sql );
		$maxCustomerGroupId = 0;
		$customerGroups = array();
		foreach ($result->rows as $row) {
			$customerGroupId = $row['customer_group_id'];
			$name = $row['name'];
			if (!isset($customerGroups[$name])) {
				$customerGroups[$name] = $customerGroupId;
			}
			if ($maxCustomerGroupId < $customerGroupId) {
				$maxCustomerGroupId = $customerGroupId;
			}
		}

		// add additional customer groups into the database
		foreach ($discounts as $discount) {
			$name = $discount['customer_group'];
			if (!isset($customerGroups[$name])) {
				$maxCustomerGroupId += 1;
				$sql  = "INSERT INTO `".DB_PREFIX."customer_group` (`customer_group_id`, `name`) VALUES "; 
				$sql .= "($maxCustomerGroupId, '$name')";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " name='$name'";
				//$sql .= ";\n";
				$database->query($sql);
				$customerGroups[$name] = $maxCustomerGroupId;
			}
		}

		// store product discounts into the database
		$productDiscountId = 0;
		$first = TRUE;
		foreach ($discounts as $discount) {
			$productDiscountId += 1;
			$productId = $discount['product_id'];
			$name = $discount['customer_group'];
			$customerGroupId = $customerGroups[$name];
			$quantity = $discount['quantity'];
			$priority = $discount['priority'];
			$price = $discount['price'];
			$dateStart = $discount['date_start'];
			$dateEnd = $discount['date_end'];
			// $sql .= ($first) ? "\n" : ",\n";
			// $first = FALSE;
			$sql = "INSERT INTO `".DB_PREFIX."product_discount` (`product_discount_id`,`product_id`,`customer_group_id`,`quantity`,`priority`,`price`,`date_start`,`date_end` ) VALUES "; 

			$sql .= "($productDiscountId,$productId,$customerGroupId,$quantity,$priority,$price,'$dateStart','$dateEnd')";
			$sql .= " ON DUPLICATE KEY UPDATE ";
			$sql .= " product_id=$productId, customer_group_id=$customerGroupId, quantity=$quantity, priority=$priority,";
			$sql .= " price=$price, date_start='$dateStart', date_end='$dateEnd'";	
			$database->query($sql);
		}
		// if (!$first) {
			// $database->query($sql);
		// }

		$database->query("COMMIT;");
		return TRUE;
	}


	function uploadDiscounts( &$reader, &$database ) 
	{
		$data = $reader->getSheet(5);
		$discounts = array();
		$i = 0;
		$k = $data->getHighestRow();
		$isFirstRow = TRUE;
		for ($i=0; $i<$k; $i+=1) {
			$j = 1;
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$productId = trim($this->getCell($data,$i,$j++));
			if ($productId=="") {
				continue;
			}
			$customerGroup = trim($this->getCell($data,$i,$j++));
			if ($customerGroup=="") {
				continue;
			}
			$quantity = $this->getCell($data,$i,$j++,'0');
			$priority = $this->getCell($data,$i,$j++,'0');
			$price = $this->getCell($data,$i,$j++,'0');
			$dateStart = $this->getCell($data,$i,$j++,'0000-00-00');
			$dateEnd = $this->getCell($data,$i,$j++,'0000-00-00');
			$discounts[$i] = array();
			$discounts[$i]['product_id'] = $productId;
			$discounts[$i]['customer_group'] = $customerGroup;
			$discounts[$i]['quantity'] = $quantity;
			$discounts[$i]['priority'] = $priority;
			$discounts[$i]['price'] = $price;
			$discounts[$i]['date_start'] = $dateStart;
			$discounts[$i]['date_end'] = $dateEnd;
		}
		return $this->storeDiscountsIntoDatabase( $database, $discounts );
	}



	function storeRewardsIntoDatabase( &$database, &$rewards )
	{
		$sql = "START TRANSACTION;\n";
		// $sql .= "DELETE FROM `".DB_PREFIX."product_reward`;\n";
		$this->import( $database, $sql );

		// find existing customer groups from the database
		$sql = "SELECT * FROM `".DB_PREFIX."customer_group`";
		$result = $database->query( $sql );
		$maxCustomerGroupId = 0;
		$customerGroups = array();
		foreach ($result->rows as $row) {
			$customerGroupId = $row['customer_group_id'];
			$name = $row['name'];
			if (!isset($customerGroups[$name])) {
				$customerGroups[$name] = $customerGroupId;
			}
			if ($maxCustomerGroupId < $customerGroupId) {
				$maxCustomerGroupId = $customerGroupId;
			}
		}

		// add additional customer groups into the database
		foreach ($rewards as $reward) {
			$name = $reward['customer_group'];
			if (!isset($customerGroups[$name])) {
				$maxCustomerGroupId += 1;
				$sql  = "INSERT INTO `".DB_PREFIX."customer_group` (`customer_group_id`, `name`) VALUES "; 
				$sql .= "($maxCustomerGroupId, '$name')";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " name='$name'";	
				$database->query($sql);
				$customerGroups[$name] = $maxCustomerGroupId;
			}
		}

		// store product rewards into the database
		$productRewardId = 0;
		// $first = TRUE;
		foreach ($rewards as $reward) {
			$productRewardId += 1;
			$productId = $reward['product_id'];
			$name = $reward['customer_group'];
			$customerGroupId = $customerGroups[$name];
			$points = $reward['points'];

			$sql  = "INSERT INTO `".DB_PREFIX."product_reward` (`product_reward_id`,`product_id`,`customer_group_id`,`points` )  "; 
			$sql .= " VALUES($productRewardId,$productId,$customerGroupId,$points)";
			$sql .= " ON DUPLICATE KEY UPDATE ";
			$sql .= " product_id=$productId, customer_group_id=$customerGroupId, points=$points";
			$database->query($sql);

		}
		// if (!$first) {
			// $database->query($sql);
		// }

		$database->query("COMMIT;");
		return TRUE;
	}


	function uploadRewards( &$reader, &$database ) 
	{
		$data = $reader->getSheet(6);
		$rewards = array();
		$i = 0;
		$k = $data->getHighestRow();
		$isFirstRow = TRUE;
		for ($i=0; $i<$k; $i+=1) {
			$j= 1;
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$productId = trim($this->getCell($data,$i,$j++));
			if ($productId=="") {
				continue;
			}
			$customerGroup = trim($this->getCell($data,$i,$j++));
			if ($customerGroup=="") {
				continue;
			}
			$points = $this->getCell($data,$i,$j++,'0');
			$rewards[$i] = array();
			$rewards[$i]['product_id'] = $productId;
			$rewards[$i]['customer_group'] = $customerGroup;
			$rewards[$i]['points'] = $points;
		}
		return $this->storeRewardsIntoDatabase( $database, $rewards );
	}


	function storeAdditionalImagesIntoDatabase( &$reader, &$database )
	{
		// start transaction
		$sql = "START TRANSACTION;\n";
		
		// delete old additional product images from database
		$sql = "DELETE FROM `".DB_PREFIX."product_image`";
		$database->query( $sql );
		
		// insert new additional product images into database
		$data =& $reader->getSheet(1); // Products worksheet
		$maxImageId = 0;
		
		$k = $data->getHighestRow();
		for ($i=1; $i<$k; $i+=1) {
			$productId = trim($this->getCell($data,$i,1));
			if ($productId=="") {
				continue;
			}
			$imageNames = trim($this->getCell($data,$i,31));
			$imageNames = trim( $this->clean($imageNames, TRUE) );
			$imageNames = ($imageNames=="") ? array() : explode( ",", $imageNames );
			foreach ($imageNames as $imageName) {
				$maxImageId += 1;
				$sql  = "INSERT INTO `".DB_PREFIX."product_image` (`product_image_id`, product_id, `image`) VALUES ";
				$sql .= "($maxImageId,$productId,'$imageName')";
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " product_id = $productId, image = '$imageName'";
				
				$database->query( $sql );
			}
		}
		
		$database->query( "COMMIT;" );
		return TRUE;
	}


	function uploadImages( &$reader, &$database )
	{
		$ok = $this->storeAdditionalImagesIntoDatabase( $reader, $database );
		return $ok;
	}

	function getCell(&$worksheet,$row,$col,$default_val='') {
		$col -= 1; // we use 1-based, PHPExcel uses 0-based column index
		$row += 1; // we use 0-based, PHPExcel used 1-based row index
		return ($worksheet->cellExistsByColumnAndRow($col,$row)) ? $worksheet->getCellByColumnAndRow($col,$row)->getValue() : $default_val;
	}

	function validateHeading( &$data, &$expected ) {
		$heading = array();
		$k = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
		if ($k != count($expected)) {
			return FALSE;
		}
		$i = 0;
		for ($j=1; $j <= $k; $j+=1) {
			$heading[] = $this->getCell($data,$i,$j);
		}
		$valid = TRUE;
		for ($i=0; $i < count($expected); $i+=1) {
			if (!isset($heading[$i])) {
				$valid = FALSE;
				break;
			}
			if (strtolower($heading[$i]) != strtolower($expected[$i])) {
				$valid = FALSE;
				break;
			}
		}
		return $valid;
	}


	function validateCategories( &$reader )
	{
		$expectedCategoryHeading = array
		( "category_id", "parent_id", "name", "top", "columns", "sort_order", "image_name", "date_added", "date_modified", "language_id", "seo_keyword", "description", "meta_description", "meta_keywords", "store_ids", "layout", "status\nenabled" );
		$data =& $reader->getSheet(0);
		return $this->validateHeading( $data, $expectedCategoryHeading );
	}


	function validateProducts( &$reader )
	{
		$expectedProductHeading = array
		( "product_id", "name", "categories", "sku", "upc", "location", "quantity", "model", "manufacturer", "image_name", "requires\nshipping", "price", "points", "date_added", "date_modified", "date_available", "weight", "unit", "length", "width", "height", "length\nunit", "status\nenabled", "tax_class_id", "viewed", "language_id", "seo_keyword", "description", "meta_description", "meta_keywords", "additional image names", "stock_status_id", "store_ids", "layout", "related_ids", "tags", "sort_order", "subtract", "minimum" );
		$data =& $reader->getSheet(1);
		return $this->validateHeading( $data, $expectedProductHeading );
	}


	function validateOptions( &$reader )
	{
		$expectedOptionHeading = array
		( "product_id", "language_id", "option", "type", "value", "image", "required", "quantity", "subtract", "price", "price\nprefix", "points", "points\nprefix", "weight", "weight\nprefix", "sort_order" );
		$data =& $reader->getSheet(2);
		return $this->validateHeading( $data, $expectedOptionHeading );
	}


	function validateAttributes( &$reader )
	{
		$expectedAttributeHeading = array
		( "product_id", "language_id", "attribute_group", "attribute_name", "text", "sort_order" );
		$data =& $reader->getSheet(3);
		return $this->validateHeading( $data, $expectedAttributeHeading );
	}


	function validateSpecials( &$reader )
	{
		$expectedSpecialsHeading = array
		( "product_id", "customer_group", "priority", "price", "date_start", "date_end" );
		$data =& $reader->getSheet(4);
		return $this->validateHeading( $data, $expectedSpecialsHeading );
	}


	function validateDiscounts( &$reader )
	{
		$expectedDiscountsHeading = array
		( "product_id", "customer_group", "quantity", "priority", "price", "date_start", "date_end" );
		$data =& $reader->getSheet(5);
		return $this->validateHeading( $data, $expectedDiscountsHeading );
	}


	function validateRewards( &$reader )
	{
		$expectedRewardsHeading = array
		( "product_id", "customer_group", "points" );
		$data =& $reader->getSheet(6);
		return $this->validateHeading( $data, $expectedRewardsHeading );
	}


	function validateUpload( &$reader )
	{
		if ($reader->getSheetCount() != 7) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get( 'error_sheet_count' )."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		if (!$this->validateCategories( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get('error_categories_header')."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		if (!$this->validateProducts( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get('error_products_header')."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		if (!$this->validateOptions( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get('error_options_header')."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		if (!$this->validateAttributes( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get('error_attributes_header')."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		if (!$this->validateSpecials( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get('error_specials_header')."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		if (!$this->validateDiscounts( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get('error_discounts_header')."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		if (!$this->validateRewards( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get('error_rewards_header')."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		return TRUE;
	}


	function clearCache() {
		$this->cache->delete('category');
		$this->cache->delete('category_description');
		$this->cache->delete('manufacturer');
		$this->cache->delete('product');
		$this->cache->delete('product_image');
		$this->cache->delete('product_option');
		$this->cache->delete('product_option_description');
		$this->cache->delete('product_option_value');
		$this->cache->delete('product_option_value_description');
		$this->cache->delete('product_to_category');
		$this->cache->delete('url_alias');
		$this->cache->delete('product_special');
		$this->cache->delete('product_discount');
	}


	function upload( $filename ) {
		global $config;
		global $log;
		$config = $this->config;
		$log = $this->log;
		set_error_handler('error_handler_for_export',E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export');
		$database =& $this->db;
		ini_set("memory_limit","512M");
		ini_set("max_execution_time",180);
		//set_time_limit( 60 );
		chdir( '../system/PHPExcel' );
		require_once( 'Classes/PHPExcel.php' );
		chdir( '../../admin' );
		$inputFileType = PHPExcel_IOFactory::identify($filename);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$reader = $objReader->load($filename);
		$ok = $this->validateUpload( $reader );
		if (!$ok) {
			return FALSE;
		}
		$this->clearCache();
		$ok = $this->uploadImages( $reader, $database );
		if (!$ok) {
			return FALSE;
		}
		$ok = $this->uploadCategories( $reader, $database );
		if (!$ok) {
			return FALSE;
		}
		$ok = $this->uploadProducts( $reader, $database );
		if (!$ok) {
			return FALSE;
		}
		$ok = $this->uploadOptions( $reader, $database );
		if (!$ok) {
			return FALSE;
		}
		$ok = $this->uploadAttributes( $reader, $database );
		if (!$ok) {
			return FALSE;
		}
		$ok = $this->uploadSpecials( $reader, $database );
		if (!$ok) {
			return FALSE;
		}
		$ok = $this->uploadDiscounts( $reader, $database );
		if (!$ok) {
			return FALSE;
		}
		$ok = $this->uploadRewards( $reader, $database );
		if (!$ok) {
			return FALSE;
		}
		chdir( '../../..' );
		return $ok;
	}



	function getStoreIdsForCategories( &$database ) {
		$sql =  "SELECT category_id, store_id FROM `".DB_PREFIX."category_to_store` cs;";
		$storeIds = array();
		$result = $database->query( $sql );
		foreach ($result->rows as $row) {
			$categoryId = $row['category_id'];
			$storeId = $row['store_id'];
			if (!isset($storeIds[$categoryId])) {
				$storeIds[$categoryId] = array();
			}
			if (!in_array($storeId,$storeIds[$categoryId])) {
				$storeIds[$categoryId][] = $storeId;
			}
		}
		return $storeIds;
	}


	function getLayoutsForCategories( &$database ) {
		$sql  = "SELECT cl.*, l.name FROM `".DB_PREFIX."category_to_layout` cl ";
		$sql .= "LEFT JOIN `".DB_PREFIX."layout` l ON cl.layout_id = l.layout_id ";
		$sql .= "ORDER BY cl.category_id, cl.store_id;";
		$result = $database->query( $sql );
		$layouts = array();
		foreach ($result->rows as $row) {
			$categoryId = $row['category_id'];
			$storeId = $row['store_id'];
			$name = $row['name'];
			if (!isset($layouts[$categoryId])) {
				$layouts[$categoryId] = array();
			}
			$layouts[$categoryId][$storeId] = $name;
		}
		return $layouts;
	}


	function populateCategoriesWorksheet( &$worksheet, &$database, $languageId, &$boxFormat, &$textFormat )
	{
		// Set the column widths
		$j = 0;
		$worksheet->setColumn($j,$j++,strlen('category_id')+1);
		$worksheet->setColumn($j,$j++,strlen('parent_id')+1);
		$worksheet->setColumn($j,$j++,max(strlen('name'),32)+1);
		$worksheet->setColumn($j,$j++,max(strlen('top'),5)+1);
		$worksheet->setColumn($j,$j++,strlen('columns')+1);
		$worksheet->setColumn($j,$j++,strlen('sort_order')+1);
		$worksheet->setColumn($j,$j++,max(strlen('image_name'),12)+1);
		$worksheet->setColumn($j,$j++,max(strlen('date_added'),19)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('date_modified'),19)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('language_id'),2)+1);
		$worksheet->setColumn($j,$j++,max(strlen('seo_keyword'),16)+1);
		$worksheet->setColumn($j,$j++,max(strlen('description'),32)+1);
		$worksheet->setColumn($j,$j++,max(strlen('meta_description'),32)+1);
		$worksheet->setColumn($j,$j++,max(strlen('meta_keywords'),32)+1);
		$worksheet->setColumn($j,$j++,max(strlen('store_ids'),16)+1);
		$worksheet->setColumn($j,$j++,max(strlen('layout'),16)+1);
		$worksheet->setColumn($j,$j++,max(strlen('status'),5)+1,$textFormat);
		
		// The heading row
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'category_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'parent_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'name', $boxFormat );
		$worksheet->writeString( $i, $j++, 'top', $boxFormat );
		$worksheet->writeString( $i, $j++, 'columns', $boxFormat );
		$worksheet->writeString( $i, $j++, 'sort_order', $boxFormat );
		$worksheet->writeString( $i, $j++, 'image_name', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_added', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_modified', $boxFormat );
		$worksheet->writeString( $i, $j++, 'language_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'seo_keyword', $boxFormat );
		$worksheet->writeString( $i, $j++, 'description', $boxFormat );
		$worksheet->writeString( $i, $j++, 'meta_description', $boxFormat );
		$worksheet->writeString( $i, $j++, 'meta_keywords', $boxFormat );
		$worksheet->writeString( $i, $j++, 'store_ids', $boxFormat );
		$worksheet->writeString( $i, $j++, 'layout', $boxFormat );
		$worksheet->writeString( $i, $j++, "status\nenabled", $boxFormat );
		$worksheet->setRow( $i, 30, $boxFormat );
		
		// The actual categories data
		$i += 1;
		$j = 0;
		$storeIds = $this->getStoreIdsForCategories( $database );
		$layouts = $this->getLayoutsForCategories( $database );
		$query  = "SELECT c.* , cd.*, ua.keyword FROM `".DB_PREFIX."category` c ";
		$query .= "INNER JOIN `".DB_PREFIX."category_description` cd ON cd.category_id = c.category_id ";
		$query .= " AND cd.language_id=$languageId ";
		$query .= "LEFT JOIN `".DB_PREFIX."url_alias` ua ON ua.query=CONCAT('category_id=',c.category_id) ";
		$query .= "ORDER BY c.`parent_id`, `sort_order`, c.`category_id`;";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$worksheet->setRow( $i, 26 );
			$worksheet->write( $i, $j++, $row['category_id'] );
			$worksheet->write( $i, $j++, $row['parent_id'] );
			$worksheet->writeString( $i, $j++, html_entity_decode($row['name'],ENT_QUOTES,'UTF-8') );
			$worksheet->write( $i, $j++, ($row['top']==0) ? "false" : "true", $textFormat );
			$worksheet->write( $i, $j++, $row['column'] );
			$worksheet->write( $i, $j++, $row['sort_order'] );
			$worksheet->write( $i, $j++, $row['image'] );
			$worksheet->write( $i, $j++, $row['date_added'], $textFormat );
			$worksheet->write( $i, $j++, $row['date_modified'], $textFormat );
			$worksheet->write( $i, $j++, $row['language_id'] );
			$worksheet->writeString( $i, $j++, ($row['keyword']) ? $row['keyword'] : '' );
			$worksheet->writeString( $i, $j++, html_entity_decode($row['description'],ENT_QUOTES,'UTF-8') );
			$worksheet->writeString( $i, $j++, html_entity_decode($row['meta_description'],ENT_QUOTES,'UTF-8') );
			$worksheet->writeString( $i, $j++, html_entity_decode($row['meta_keyword'],ENT_QUOTES,'UTF-8') );
			$storeIdList = '';
			$categoryId = $row['category_id'];
			if (isset($storeIds[$categoryId])) {
				foreach ($storeIds[$categoryId] as $storeId) {
					$storeIdList .= ($storeIdList=='') ? $storeId : ','.$storeId;
				}
			}
			$worksheet->write( $i, $j++, $storeIdList, $textFormat );
			$layoutList = '';
			if (isset($layouts[$categoryId])) {
				foreach ($layouts[$categoryId] as $storeId => $name) {
					$layoutList .= ($layoutList=='') ? $storeId.':'.$name : ','.$storeId.':'.$name;
				}
			}
			$worksheet->write( $i, $j++, $layoutList, $textFormat );
			$worksheet->write( $i, $j++, ($row['status']==0) ? "false" : "true", $textFormat );
			$i += 1;
			$j = 0;
		}
	}


	function getStoreIdsForProducts( &$database ) {
		$sql =  "SELECT product_id, store_id FROM `".DB_PREFIX."product_to_store` ps;";
		$storeIds = array();
		$result = $database->query( $sql );
		foreach ($result->rows as $row) {
			$productId = $row['product_id'];
			$storeId = $row['store_id'];
			if (!isset($storeIds[$productId])) {
				$storeIds[$productId] = array();
			}
			if (!in_array($storeId,$storeIds[$productId])) {
				$storeIds[$productId][] = $storeId;
			}
		}
		return $storeIds;
	}


	function getLayoutsForProducts( &$database ) {
		$sql  = "SELECT pl.*, l.name FROM `".DB_PREFIX."product_to_layout` pl ";
		$sql .= "LEFT JOIN `".DB_PREFIX."layout` l ON pl.layout_id = l.layout_id ";
		$sql .= "ORDER BY pl.product_id, pl.store_id;";
		$result = $database->query( $sql );
		$layouts = array();
		foreach ($result->rows as $row) {
			$productId = $row['product_id'];
			$storeId = $row['store_id'];
			$name = $row['name'];
			if (!isset($layouts[$productId])) {
				$layouts[$productId] = array();
			}
			$layouts[$productId][$storeId] = $name;
		}
		return $layouts;
	}


	function populateProductsWorksheet( &$worksheet, &$database, &$imageNames, $languageId, &$priceFormat, &$boxFormat, &$weightFormat, &$textFormat )
	{
		// Set the column widths
		$j = 0;
		$worksheet->setColumn($j,$j++,max(strlen('product_id'),4)+1);
		$worksheet->setColumn($j,$j++,max(strlen('name'),30)+1);
		$worksheet->setColumn($j,$j++,max(strlen('categories'),12)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('sku'),10)+1);
		$worksheet->setColumn($j,$j++,max(strlen('upc'),10)+1);
		$worksheet->setColumn($j,$j++,max(strlen('location'),10)+1);
		$worksheet->setColumn($j,$j++,max(strlen('quantity'),4)+1);
		$worksheet->setColumn($j,$j++,max(strlen('model'),8)+1);
		$worksheet->setColumn($j,$j++,max(strlen('manufacturer'),10)+1);
		$worksheet->setColumn($j,$j++,max(strlen('image_name'),12)+1);
		$worksheet->setColumn($j,$j++,max(strlen('shipping'),5)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('price'),10)+1,$priceFormat);
		$worksheet->setColumn($j,$j++,max(strlen('points'),5)+1);
		$worksheet->setColumn($j,$j++,max(strlen('date_added'),19)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('date_modified'),19)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('date_available'),10)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('weight'),6)+1,$weightFormat);
		$worksheet->setColumn($j,$j++,max(strlen('unit'),3)+1);
		$worksheet->setColumn($j,$j++,max(strlen('length'),8)+1);
		$worksheet->setColumn($j,$j++,max(strlen('width'),8)+1);
		$worksheet->setColumn($j,$j++,max(strlen('height'),8)+1);
		$worksheet->setColumn($j,$j++,max(strlen('length'),3)+1);
		$worksheet->setColumn($j,$j++,max(strlen('status'),5)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('tax_class_id'),2)+1);
		$worksheet->setColumn($j,$j++,max(strlen('viewed'),5)+1);
		$worksheet->setColumn($j,$j++,max(strlen('language_id'),2)+1);
		$worksheet->setColumn($j,$j++,max(strlen('seo_keyword'),16)+1);
		$worksheet->setColumn($j,$j++,max(strlen('description'),32)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('meta_description'),32)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('meta_keywords'),32)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('additional image names'),24)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('stock_status_id'),3)+1);
		$worksheet->setColumn($j,$j++,max(strlen('store_ids'),16)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('layout'),16)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('related_ids'),16)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('tags'),32)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('sort_order'),8)+1);
		$worksheet->setColumn($j,$j++,max(strlen('subtract'),5)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('minimum'),8)+1);

		// The product headings row
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'product_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'name', $boxFormat );
		$worksheet->writeString( $i, $j++, 'categories', $boxFormat );
		$worksheet->writeString( $i, $j++, 'sku', $boxFormat );
		$worksheet->writeString( $i, $j++, 'upc', $boxFormat );
		$worksheet->writeString( $i, $j++, 'location', $boxFormat );
		$worksheet->writeString( $i, $j++, 'quantity', $boxFormat );
		$worksheet->writeString( $i, $j++, 'model', $boxFormat );
		$worksheet->writeString( $i, $j++, 'manufacturer', $boxFormat );
		$worksheet->writeString( $i, $j++, 'image_name', $boxFormat );
		$worksheet->writeString( $i, $j++, "requires\nshipping", $boxFormat );
		$worksheet->writeString( $i, $j++, 'price', $boxFormat );
		$worksheet->writeString( $i, $j++, 'points', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_added', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_modified', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_available', $boxFormat );
		$worksheet->writeString( $i, $j++, 'weight', $boxFormat );
		$worksheet->writeString( $i, $j++, 'unit', $boxFormat );
		$worksheet->writeString( $i, $j++, 'length', $boxFormat );
		$worksheet->writeString( $i, $j++, 'width', $boxFormat );
		$worksheet->writeString( $i, $j++, 'height', $boxFormat );
		$worksheet->writeString( $i, $j++, "length\nunit", $boxFormat );
		$worksheet->writeString( $i, $j++, "status\nenabled", $boxFormat );
		$worksheet->writeString( $i, $j++, 'tax_class_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'viewed', $boxFormat );
		$worksheet->writeString( $i, $j++, 'language_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'seo_keyword', $boxFormat );
		$worksheet->writeString( $i, $j++, 'description', $boxFormat );
		$worksheet->writeString( $i, $j++, 'meta_description', $boxFormat );
		$worksheet->writeString( $i, $j++, 'meta_keywords', $boxFormat );
		$worksheet->writeString( $i, $j++, 'additional image names', $boxFormat );
		$worksheet->writeString( $i, $j++, 'stock_status_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'store_ids', $boxFormat );
		$worksheet->writeString( $i, $j++, 'layout', $boxFormat );
		$worksheet->writeString( $i, $j++, 'related_ids', $boxFormat );
		$worksheet->writeString( $i, $j++, 'tags', $boxFormat );
		$worksheet->writeString( $i, $j++, 'sort_order', $boxFormat );
		$worksheet->writeString( $i, $j++, "subtract", $boxFormat );
		$worksheet->writeString( $i, $j++, 'minimum', $boxFormat );
		$worksheet->setRow( $i, 30, $boxFormat );
		
		// The actual products data
		$i += 1;
		$j = 0;
		$storeIds = $this->getStoreIdsForProducts( $database );
		$layouts = $this->getLayoutsForProducts( $database );
		$query  = "SELECT ";
		$query .= "  p.product_id,";
		$query .= "  pd.name,";
		$query .= "  GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ) AS categories,";
		$query .= "  p.sku,";
		$query .= "  p.upc,";
		$query .= "  p.location,";
		$query .= "  p.quantity,";
		$query .= "  p.model,";
		$query .= "  m.name AS manufacturer,";
		$query .= "  p.image AS image_name,";
		$query .= "  p.shipping,";
		$query .= "  p.price,";
		$query .= "  p.points,";
		$query .= "  p.date_added,";
		$query .= "  p.date_modified,";
		$query .= "  p.date_available,";
		$query .= "  p.weight,";
		$query .= "  wc.unit,";
		$query .= "  p.length,";
		$query .= "  p.width,";
		$query .= "  p.height,";
		$query .= "  p.status,";
		$query .= "  p.tax_class_id,";
		$query .= "  p.viewed,";
		$query .= "  p.sort_order,";
		$query .= "  pd.language_id,";
		$query .= "  ua.keyword,";
		$query .= "  pd.description, ";
		$query .= "  pd.meta_description, ";
		$query .= "  pd.meta_keyword, ";
		$query .= "  p.stock_status_id, ";
		$query .= "  mc.unit AS length_unit, ";
		$query .= "  p.subtract, ";
		$query .= "  p.minimum, ";
		$query .= "  GROUP_CONCAT( DISTINCT CAST(pr.related_id AS CHAR(11)) SEPARATOR \",\" ) AS related, ";
		$query .= "  GROUP_CONCAT( DISTINCT pt.tag SEPARATOR \",\" ) AS tags ";
		$query .= "FROM `".DB_PREFIX."product` p ";
		$query .= "LEFT JOIN `".DB_PREFIX."product_description` pd ON p.product_id=pd.product_id ";
		$query .= "  AND pd.language_id=$languageId ";
		$query .= "LEFT JOIN `".DB_PREFIX."product_to_category` pc ON p.product_id=pc.product_id ";
		$query .= "LEFT JOIN `".DB_PREFIX."url_alias` ua ON ua.query=CONCAT('product_id=',p.product_id) ";
		$query .= "LEFT JOIN `".DB_PREFIX."manufacturer` m ON m.manufacturer_id = p.manufacturer_id ";
		$query .= "LEFT JOIN `".DB_PREFIX."weight_class_description` wc ON wc.weight_class_id = p.weight_class_id ";
		$query .= "  AND wc.language_id=$languageId ";
		$query .= "LEFT JOIN `".DB_PREFIX."length_class_description` mc ON mc.length_class_id=p.length_class_id ";
		$query .= "  AND mc.language_id=$languageId ";
		$query .= "LEFT JOIN `".DB_PREFIX."product_related` pr ON pr.product_id=p.product_id ";
		$query .= "LEFT JOIN `".DB_PREFIX."product_tag` pt ON pt.product_id=p.product_id ";
		$query .= "  AND pt.language_id=$languageId ";
		$query .= "GROUP BY p.product_id ";
		$query .= "ORDER BY p.product_id, pc.category_id; ";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$worksheet->setRow( $i, 26 );
			$productId = $row['product_id'];
			$worksheet->write( $i, $j++, $productId );
			$worksheet->writeString( $i, $j++, html_entity_decode($row['name'],ENT_QUOTES,'UTF-8') );
			$worksheet->write( $i, $j++, $row['categories'], $textFormat );
			$worksheet->writeString( $i, $j++, $row['sku'] );
			$worksheet->writeString( $i, $j++, $row['upc'] );
			$worksheet->writeString( $i, $j++, $row['location'] );
			$worksheet->write( $i, $j++, $row['quantity'] );
			$worksheet->writeString( $i, $j++, $row['model'] );
			$worksheet->writeString( $i, $j++, $row['manufacturer'] );
			$worksheet->writeString( $i, $j++, $row['image_name'] );
			$worksheet->write( $i, $j++, ($row['shipping']==0) ? "no" : "yes", $textFormat );
			$worksheet->write( $i, $j++, $row['price'], $priceFormat );
			$worksheet->write( $i, $j++, $row['points'] );
			$worksheet->write( $i, $j++, $row['date_added'], $textFormat );
			$worksheet->write( $i, $j++, $row['date_modified'], $textFormat );
			$worksheet->write( $i, $j++, $row['date_available'], $textFormat );
			$worksheet->write( $i, $j++, $row['weight'], $weightFormat );
			$worksheet->writeString( $i, $j++, $row['unit'] );
			$worksheet->write( $i, $j++, $row['length'] );
			$worksheet->write( $i, $j++, $row['width'] );
			$worksheet->write( $i, $j++, $row['height'] );
			$worksheet->writeString( $i, $j++, $row['length_unit'] );
			$worksheet->write( $i, $j++, ($row['status']==0) ? "false" : "true", $textFormat );
			$worksheet->write( $i, $j++, $row['tax_class_id'] );
			$worksheet->write( $i, $j++, $row['viewed'] );
			$worksheet->write( $i, $j++, $row['language_id'] );
			$worksheet->writeString( $i, $j++, ($row['keyword']) ? $row['keyword'] : '' );
			$worksheet->writeString( $i, $j++, html_entity_decode($row['description'],ENT_QUOTES,'UTF-8'), $textFormat, TRUE );
			$worksheet->write( $i, $j++, html_entity_decode($row['meta_description'],ENT_QUOTES,'UTF-8'), $textFormat );
			$worksheet->write( $i, $j++, html_entity_decode($row['meta_keyword'],ENT_QUOTES,'UTF-8'), $textFormat );
			$names = "";
			if (isset($imageNames[$productId])) {
				$first = TRUE;
				foreach ($imageNames[$productId] AS $name) {
					if (!$first) {
						$names .= ",\n";
					}
					$first = FALSE;
					$names .= $name;
				}
			}
			$worksheet->write( $i, $j++, $names, $textFormat );
			$worksheet->write( $i, $j++, $row['stock_status_id'] );
			$storeIdList = '';
			if (isset($storeIds[$productId])) {
				foreach ($storeIds[$productId] as $storeId) {
					$storeIdList .= ($storeIdList=='') ? $storeId : ','.$storeId;
				}
			}
			$worksheet->write( $i, $j++, $storeIdList, $textFormat );
			$layoutList = '';
			if (isset($layouts[$productId])) {
				foreach ($layouts[$productId] as $storeId => $name) {
					$layoutList .= ($layoutList=='') ? $storeId.':'.$name : ','.$storeId.':'.$name;
				}
			}
			$worksheet->write( $i, $j++, $layoutList, $textFormat );
			$worksheet->write( $i, $j++, $row['related'], $textFormat );
			$worksheet->write( $i, $j++, $row['tags'], $textFormat );
			$worksheet->write( $i, $j++, $row['sort_order'] );
			$worksheet->write( $i, $j++, ($row['subtract']==0) ? "false" : "true", $textFormat );
			$worksheet->write( $i, $j++, $row['minimum'] );
			$i += 1;
			$j = 0;
		}
	}


	function populateOptionsWorksheet( &$worksheet, &$database, $languageId, &$priceFormat, &$boxFormat, &$weightFormat, $textFormat )
	{
		// Set the column widths
		$j = 0;
		$worksheet->setColumn($j,$j++,max(strlen('product_id'),4)+1);
		$worksheet->setColumn($j,$j++,max(strlen('language_id'),2)+1);
		$worksheet->setColumn($j,$j++,max(strlen('option'),30)+1);
		$worksheet->setColumn($j,$j++,max(strlen('type'),10)+1);
		$worksheet->setColumn($j,$j++,max(strlen('value'),30)+1);
		$worksheet->setColumn($j,$j++,max(strlen('image'),12)+1);
		$worksheet->setColumn($j,$j++,max(strlen('required'),5)+1);
		$worksheet->setColumn($j,$j++,max(strlen('quantity'),4)+1);
		$worksheet->setColumn($j,$j++,max(strlen('subtract'),5)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('price'),10)+1,$priceFormat);
		$worksheet->setColumn($j,$j++,max(strlen('price'),5)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('points'),10)+1,$priceFormat);
		$worksheet->setColumn($j,$j++,max(strlen('points'),5)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('weight'),10)+1,$priceFormat);
		$worksheet->setColumn($j,$j++,max(strlen('weight'),5)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('sort_order'),5)+1);
		
		// The options headings row
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'product_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'language_id', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'option', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'type', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'value', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'image', $boxFormat );
		$worksheet->writeString( $i, $j++, 'required', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'quantity', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'subtract', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'price', $boxFormat  );
		$worksheet->writeString( $i, $j++, "price\nprefix", $boxFormat  );
		$worksheet->writeString( $i, $j++, 'points', $boxFormat  );
		$worksheet->writeString( $i, $j++, "points\nprefix", $boxFormat  );
		$worksheet->writeString( $i, $j++, 'weight', $boxFormat  );
		$worksheet->writeString( $i, $j++, "weight\nprefix", $boxFormat  );
		$worksheet->writeString( $i, $j++, 'sort_order', $boxFormat  );
		$worksheet->setRow( $i, 30, $boxFormat );
		
		// The actual options data
		$i += 1;
		$j = 0;
		$query  = "SELECT po.product_id,";
		$query .= "  po.option_id,";
		$query .= "  po.option_value AS default_value,";
		$query .= "  po.required,";
		$query .= "  pov.option_value_id,";
		$query .= "  pov.quantity,";
		$query .= "  pov.subtract,";
		$query .= "  pov.price,";
		$query .= "  pov.price_prefix,";
		$query .= "  pov.points,";
		$query .= "  pov.points_prefix,";
		$query .= "  pov.weight,";
		$query .= "  pov.weight_prefix,";
		$query .= "  ovd.name AS option_value,";
		$query .= "  ov.image,";
		$query .= "  ov.sort_order,";
		$query .= "  od.name AS option_name,";
		$query .= "  o.type ";
		$query .= "FROM `".DB_PREFIX."product_option` po ";
		$query .= "LEFT JOIN `".DB_PREFIX."option` o ON o.option_id=po.option_id ";
		$query .= "LEFT JOIN `".DB_PREFIX."product_option_value` pov ON pov.product_option_id = po.product_option_id ";
		$query .= "LEFT JOIN `".DB_PREFIX."option_value` ov ON ov.option_value_id=pov.option_value_id ";
		$query .= "LEFT JOIN `".DB_PREFIX."option_value_description` ovd ON ovd.option_value_id=ov.option_value_id AND ovd.language_id=$languageId ";
		$query .= "LEFT JOIN `".DB_PREFIX."option_description` od ON od.option_id=o.option_id AND od.language_id=$languageId ";
		$query .= "ORDER BY po.product_id, po.option_id, pov.option_value_id;";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$worksheet->setRow( $i, 13 );
			$worksheet->write( $i, $j++, $row['product_id'] );
			$worksheet->write( $i, $j++, $languageId );
			$worksheet->writeString( $i, $j++, $row['option_name'] );
			$worksheet->writeString( $i, $j++, $row['type'] );
			$worksheet->writeString( $i, $j++, ($row['default_value']) ? $row['default_value'] : $row['option_value'] );
			$worksheet->writeString( $i, $j++, $row['image'] );
			$worksheet->write( $i, $j++, ($row['required']==0) ? "false" : "true", $textFormat );
			$worksheet->write( $i, $j++, $row['quantity'] );
			if (is_null($row['option_value_id'])) {
				$subtract = '';
			} else {
				$subtract = ($row['subtract']==0) ? "false" : "true";
			}
			$worksheet->write( $i, $j++, $subtract, $textFormat );
			$worksheet->write( $i, $j++, $row['price'], $priceFormat );
			$worksheet->writeString( $i, $j++, $row['price_prefix'], $textFormat );
			$worksheet->write( $i, $j++, $row['points'] );
			$worksheet->writeString( $i, $j++, $row['points_prefix'], $textFormat );
			$worksheet->write( $i, $j++, $row['weight'], $weightFormat );
			$worksheet->writeString( $i, $j++, $row['weight_prefix'], $textFormat );
			$worksheet->write( $i, $j++, $row['sort_order'] );
			$i += 1;
			$j = 0;
		}
	}


	function populateAttributesWorksheet( &$worksheet, &$database, $languageId, &$boxFormat, $textFormat )
	{
		// Set the column widths
		$j = 0;
		$worksheet->setColumn($j,$j++,max(strlen('product_id'),4)+1);
		$worksheet->setColumn($j,$j++,max(strlen('language_id'),2)+1);
		$worksheet->setColumn($j,$j++,max(strlen('attribute_group'),30)+1);
		$worksheet->setColumn($j,$j++,max(strlen('attribute_name'),30)+1);
		$worksheet->setColumn($j,$j++,max(strlen('text'),30)+1);
		$worksheet->setColumn($j,$j++,max(strlen('sort_order'),5)+1);
		
		// The attributes headings row
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'product_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'language_id', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'attribute_group', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'attribute_name', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'text', $boxFormat  );
		$worksheet->writeString( $i, $j++, 'sort_order', $boxFormat  );
		$worksheet->setRow( $i, 30, $boxFormat );
		
		// The actual attributes data
		$i += 1;
		$j = 0;
		$query  = "SELECT pa.*, a.attribute_group_id, ad.name AS attribute_name, a.sort_order, agd.name AS attribute_group ";
		$query .= "FROM `".DB_PREFIX."product_attribute` pa ";
		$query .= "LEFT JOIN `".DB_PREFIX."attribute` a ON a.attribute_id=pa.attribute_id ";
		$query .= "LEFT JOIN `".DB_PREFIX."attribute_description` ad ON ad.attribute_id=a.attribute_id AND ad.language_id=$languageId ";
		$query .= "LEFT JOIN `".DB_PREFIX."attribute_group_description` agd ON agd.attribute_group_id=a.attribute_group_id AND agd.language_id=$languageId ";
		$query .= "WHERE pa.language_id=$languageId ";
		$query .= "ORDER BY pa.product_id, a.attribute_group_id, a.attribute_id;";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$worksheet->setRow( $i, 13 );
			$worksheet->write( $i, $j++, $row['product_id'] );
			$worksheet->write( $i, $j++, $languageId );
			$worksheet->writeString( $i, $j++, $row['attribute_group'] );
			$worksheet->writeString( $i, $j++, $row['attribute_name'] );
			$worksheet->writeString( $i, $j++, $row['text'] );
			$worksheet->write( $i, $j++, $row['sort_order'] );
			$i += 1;
			$j = 0;
		}
	}


	function populateSpecialsWorksheet( &$worksheet, &$database, &$priceFormat, &$boxFormat, &$textFormat )
	{
		// Set the column widths
		$j = 0;
		$worksheet->setColumn($j,$j++,strlen('product_id')+1);
		$worksheet->setColumn($j,$j++,strlen('customer_group')+1);
		$worksheet->setColumn($j,$j++,strlen('priority')+1);
		$worksheet->setColumn($j,$j++,max(strlen('price'),10)+1,$priceFormat);
		$worksheet->setColumn($j,$j++,max(strlen('date_start'),19)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('date_end'),19)+1,$textFormat);
		
		// The heading row
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'product_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'customer_group', $boxFormat );
		$worksheet->writeString( $i, $j++, 'priority', $boxFormat );
		$worksheet->writeString( $i, $j++, 'price', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_start', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_end', $boxFormat );
		$worksheet->setRow( $i, 30, $boxFormat );
		
		// The actual product specials data
		$i += 1;
		$j = 0;
		$query  = "SELECT ps.*, cg.name FROM `".DB_PREFIX."product_special` ps ";
		$query .= "LEFT JOIN `".DB_PREFIX."customer_group` cg ON cg.customer_group_id=ps.customer_group_id ";
		$query .= "ORDER BY ps.product_id, cg.name";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$worksheet->setRow( $i, 13 );
			$worksheet->write( $i, $j++, $row['product_id'] );
			$worksheet->write( $i, $j++, $row['name'] );
			$worksheet->write( $i, $j++, $row['priority'] );
			$worksheet->write( $i, $j++, $row['price'], $priceFormat );
			$worksheet->write( $i, $j++, $row['date_start'], $textFormat );
			$worksheet->write( $i, $j++, $row['date_end'], $textFormat );
			$i += 1;
			$j = 0;
		}
	}


	function populateDiscountsWorksheet( &$worksheet, &$database, &$priceFormat, &$boxFormat, &$textFormat )
	{
		// Set the column widths
		$j = 0;
		$worksheet->setColumn($j,$j++,strlen('product_id')+1);
		$worksheet->setColumn($j,$j++,strlen('customer_group')+1);
		$worksheet->setColumn($j,$j++,strlen('quantity')+1);
		$worksheet->setColumn($j,$j++,strlen('priority')+1);
		$worksheet->setColumn($j,$j++,max(strlen('price'),10)+1,$priceFormat);
		$worksheet->setColumn($j,$j++,max(strlen('date_start'),19)+1,$textFormat);
		$worksheet->setColumn($j,$j++,max(strlen('date_end'),19)+1,$textFormat);
		
		// The heading row
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'product_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'customer_group', $boxFormat );
		$worksheet->writeString( $i, $j++, 'quantity', $boxFormat );
		$worksheet->writeString( $i, $j++, 'priority', $boxFormat );
		$worksheet->writeString( $i, $j++, 'price', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_start', $boxFormat );
		$worksheet->writeString( $i, $j++, 'date_end', $boxFormat );
		$worksheet->setRow( $i, 30, $boxFormat );
		
		// The actual product discounts data
		$i += 1;
		$j = 0;
		$query  = "SELECT pd.*, cg.name FROM `".DB_PREFIX."product_discount` pd ";
		$query .= "LEFT JOIN `".DB_PREFIX."customer_group` cg ON cg.customer_group_id=pd.customer_group_id ";
		$query .= "ORDER BY pd.product_id, cg.name";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$worksheet->setRow( $i, 13 );
			$worksheet->write( $i, $j++, $row['product_id'] );
			$worksheet->write( $i, $j++, $row['name'] );
			$worksheet->write( $i, $j++, $row['quantity'] );
			$worksheet->write( $i, $j++, $row['priority'] );
			$worksheet->write( $i, $j++, $row['price'], $priceFormat );
			$worksheet->write( $i, $j++, $row['date_start'], $textFormat );
			$worksheet->write( $i, $j++, $row['date_end'], $textFormat );
			$i += 1;
			$j = 0;
		}
	}


	function populateRewardsWorksheet( &$worksheet, &$database, &$boxFormat )
	{
		// Set the column widths
		$j = 0;
		$worksheet->setColumn($j,$j++,strlen('product_id')+1);
		$worksheet->setColumn($j,$j++,strlen('customer_group')+1);
		$worksheet->setColumn($j,$j++,strlen('points')+1);
		
		// The heading row
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'product_id', $boxFormat );
		$worksheet->writeString( $i, $j++, 'customer_group', $boxFormat );
		$worksheet->writeString( $i, $j++, 'points', $boxFormat );
		$worksheet->setRow( $i, 30, $boxFormat );
		
		// The actual product discounts data
		$i += 1;
		$j = 0;
		$query  = "SELECT pr.*, cg.name FROM `".DB_PREFIX."product_reward` pr ";
		$query .= "LEFT JOIN `".DB_PREFIX."customer_group` cg ON cg.customer_group_id=pr.customer_group_id ";
		$query .= "ORDER BY pr.product_id, cg.name";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$worksheet->setRow( $i, 13 );
			$worksheet->write( $i, $j++, $row['product_id'] );
			$worksheet->write( $i, $j++, $row['name'] );
			$worksheet->write( $i, $j++, $row['points'] );
			$i += 1;
			$j = 0;
		}
	}


	protected function clearSpreadsheetCache() {
		$files = glob(DIR_CACHE . 'Spreadsheet_Excel_Writer' . '*');
		
		if ($files) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					@unlink($file);
					clearstatcache();
				}
			}
		}
	}


	function download() {
		global $config;
		global $log;
		$config = $this->config;
		$log = $this->log;
		set_error_handler('error_handler_for_export',E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export');
		$database =& $this->db;
		$languageId = $this->getDefaultLanguageId($database);

		// We use the package from http://pear.php.net/package/Spreadsheet_Excel_Writer/
		chdir( '../system/pear' );
		require_once "Spreadsheet/Excel/Writer.php";
		chdir( '../../admin' );
		
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setTempDir(DIR_CACHE);
		$workbook->setVersion(8); // Use Excel97/2000 BIFF8 Format
		$priceFormat =& $workbook->addFormat(array('Size' => 10,'Align' => 'right','NumFormat' => '######0.00'));
		$boxFormat =& $workbook->addFormat(array('Size' => 10,'vAlign' => 'vequal_space' ));
		$weightFormat =& $workbook->addFormat(array('Size' => 10,'Align' => 'right','NumFormat' => '##0.00'));
		$textFormat =& $workbook->addFormat(array('Size' => 10, 'NumFormat' => "@" ));
		
		// sending HTTP headers
		$workbook->send('backup_categories_products.xls');
		
		// Creating the categories worksheet
		$worksheet =& $workbook->addWorksheet('Categories');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateCategoriesWorksheet( $worksheet, $database, $languageId, $boxFormat, $textFormat );
		$worksheet->freezePanes(array(1, 1, 1, 1));
		
		// Get all additional product images
		$imageNames = array();
		$query  = "SELECT DISTINCT ";
		$query .= "  p.product_id, ";
		$query .= "  pi.product_image_id AS image_id, ";
		$query .= "  pi.image AS filename ";
		$query .= "FROM `".DB_PREFIX."product` p ";
		$query .= "INNER JOIN `".DB_PREFIX."product_image` pi ON pi.product_id=p.product_id ";
		$query .= "ORDER BY product_id, image_id; ";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$productId = $row['product_id'];
			$imageId = $row['image_id'];
			$imageName = $row['filename'];
			if (!isset($imageNames[$productId])) {
				$imageNames[$productId] = array();
				$imageNames[$productId][$imageId] = $imageName;
			}
			else {
				$imageNames[$productId][$imageId] = $imageName;
			}
		}
		
		// Creating the products worksheet
		$worksheet =& $workbook->addWorksheet('Products');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateProductsWorksheet( $worksheet, $database, $imageNames, $languageId, $priceFormat, $boxFormat, $weightFormat, $textFormat );
		$worksheet->freezePanes(array(1, 1, 1, 1));
		
		// Creating the options worksheet
		$worksheet =& $workbook->addWorksheet('Options');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateOptionsWorksheet( $worksheet, $database, $languageId, $priceFormat, $boxFormat, $weightFormat, $textFormat );
		$worksheet->freezePanes(array(1, 1, 1, 1));
		
		// Creating the attributes worksheet
		$worksheet =& $workbook->addWorksheet('Attributes');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateAttributesWorksheet( $worksheet, $database, $languageId, $boxFormat, $textFormat );
		$worksheet->freezePanes(array(1, 1, 1, 1));
		
		// Creating the specials worksheet
		$worksheet =& $workbook->addWorksheet('Specials');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateSpecialsWorksheet( $worksheet, $database, $priceFormat, $boxFormat, $textFormat );
		$worksheet->freezePanes(array(1, 1, 1, 1));
		
		// Creating the discounts worksheet
		$worksheet =& $workbook->addWorksheet('Discounts');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateDiscountsWorksheet( $worksheet, $database, $priceFormat, $boxFormat, $textFormat );
		$worksheet->freezePanes(array(1, 1, 1, 1));
		
		// Creating the rewards worksheet
		$worksheet =& $workbook->addWorksheet('Rewards');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateRewardsWorksheet( $worksheet, $database, $boxFormat );
		$worksheet->freezePanes(array(1, 1, 1, 1));
		
		// Let's send the file
		$workbook->close();
		
		// Clear the spreadsheet caches
		$this->clearSpreadsheetCache();
		exit;
	}


}
?>