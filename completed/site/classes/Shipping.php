<?php

class Shipping extends Application {

    private $_table = 'shipping';
    private $_table_2 = 'shipping_type';
    private $_table_3 = 'zones';
    private $_table_4 = 'zones_post_codes';

    public $objBasket;




    public function __construct($objBasket = null) {

        parent::__construct();

        $this->objBasket = is_object($objBasket) ? $objBasket : new Basket();

    }
	
	
	
	
	
	
	
	public function getType($id = null) {
		if (!empty($id)) {
			$sql = "SELECT *
					FROM `{$this->_table_2}`
					WHERE `id` = ".intval($id);
			return $this->db->fetchOne($sql);
		}
	}
	
	
	
	
	
	
	
	public function getZones() {
		$sql = "SELECT `z`.*,
				(
					SELECT GROUP_CONCAT(`post_code` ORDER BY `post_code` ASC SEPARATOR ', ')
					FROM `{$this->_table_4}`
					WHERE `zone` = `z`.`id`
				) AS `post_codes`
				FROM `{$this->_table_3}` `z`
				ORDER BY `z`.`name` ASC";
		return $this->db->fetchAll($sql);
	}
	
	
	
	
	
	public function getTypes($local = 0) {
		$sql = "SELECT *
				FROM `{$this->_table_2}`
				WHERE `local` = ".intval($local)."
				ORDER BY `order` ASC";
		return $this->db->fetchAll($sql);
	}
	
	
	
	
	
	
	
	
	
	private function getLastType($local = 0) {
		$sql = "SELECT `order`
				FROM `{$this->_table_2}`
				WHERE `local` = {$local}
				ORDER BY `order` DESC
				LIMIT 0, 1";
		return $this->db->fetchOne($sql);
	}
	
	
	
	
	
	
	
	
	
	
	
	public function addType($params = null) {
		if (!empty($params)) {
			$params['local'] = !empty($params['local']) ? 1 : 0;
			$last = $this->getLastType($params['local']);
			$params['order'] = !empty($last) ? $last['order'] + 1 : 1;
			$this->db->prepareInsert($params);
			return $this->db->insert($this->_table_2);
		}
		return false;
	}
	
	
	
	
	public function removeType($id = null) {
		if (!empty($id)) {
			$sql = "DELETE FROM `{$this->_table_2}`
					WHERE `id` = ".intval($id);
			if ($this->db->query($sql)) {
				$sql = "DELETE FROM `{$this->_table}`
						WHERE `type` = ".intval($id);
				return $this->db->query($sql);
			}
			return false;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	public function updateType($params = null, $id = null) {
		if (!empty($params) && !empty($id)) {
			$this->db->prepareUpdate($params);
			return $this->db->update($this->_table_2, $id);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	public function setTypeDefault($id = null, $local = 0) {
		if (!empty($id)) {
			$sql = "UPDATE `{$this->_table_2}`
					SET `default` = 0
					WHERE `local` = {$local}
					AND `id` != ".intval($id);
			if ($this->db->query($sql)) {
				$sql = "UPDATE `{$this->_table_2}`
						SET `default` = 1
						WHERE `local` = {$local}
						AND `id` = ".intval($id);
				return $this->db->query($sql);
			}
			return false;
		}
		return false;
	}
	
	
	
	
	
	public function duplicateType($id = null) {
		
		$type = $this->getType($id);
		
		if (!empty($type)) {
			
			$last = $this->getLastType($type['local']);
			$order = !empty($last) ? $last['order'] + 1 : 1;
			
			$this->db->prepareInsert(array(
				'name' => $type['name'].' copy',
				'order' => $order,
				'local' => $type['local'],
				'active' => 0
			));
			
			if ($this->db->insert($this->_table_2)) {
				
				$this->db->_insert_keys = array();
				$this->db->_insert_values = array();
				
				$newId = $this->db->_id;
				
				$sql = "SELECT *
						FROM `{$this->_table}`
						WHERE `type` = {$id}";
				$list = $this->db->fetchAll($sql);
				
				if (!empty($list)) {
					
					foreach($list as $row) {
						
						$this->db->prepareInsert(array(
							'type' => $newId,
							'zone' => $row['zone'],
							'country' => $row['country'],
							'weight' => $row['weight'],
							'cost' => $row['cost']
						));						
						
						$this->db->insert($this->_table);
						
						// clear all internal arrays
						$this->db->_insert_keys = array();
						$this->db->_insert_values = array();
						
					}
					
				}
				
				return true;
				
			}
			
			return false;
			
		}
		
		return false;
		
	}
	
	
	
	
	
	
	
	
	public function getZoneById($id = null) {
		if (!empty($id)) {
			$sql = "SELECT *
					FROM `{$this->_table_3}`
					WHERE `id` = ".intval($id);
			return $this->db->fetchOne($sql);
		}
	}
	
	
	
	
	
	public function getShippingByTypeZone($typeId = null, $zoneId = null) {
		if (!empty($typeId) && !empty($zoneId)) {
			$sql = "SELECT `s`.*,
					IF (
						(
							SELECT COUNT(`weight`)
							FROM `{$this->_table}`
							WHERE `type` = `s`.`type`
							AND `zone` = `s`.`zone`
							AND `weight` < `s`.`weight`
							ORDER BY `weight` DESC
							LIMIT 0, 1
						) > 0,
						(
							SELECT `weight`
							FROM `{$this->_table}`
							WHERE `type` = `s`.`type`
							AND `zone` = `s`.`zone`
							AND `weight` < `s`.`weight`
							ORDER BY `weight` DESC
							LIMIT 0, 1
						) + 0.01,
						0
					) AS `weight_from`
					FROM `{$this->_table}` `s`
					WHERE `s`.`type` = ".intval($typeId)."
					AND `s`.`zone` = ".intval($zoneId)."
					ORDER BY `s`.`weight` ASC";
			return $this->db->fetchAll($sql);
		}
	}
	
	
	
	
	
	
	
	public function isDuplicateLocal($typeId = null, $zoneId = null, $weight = null) {
		if (!empty($typeId) && !empty($zoneId) && !empty($weight)) {
			$sql = "SELECT *
					FROM `{$this->_table}`
					WHERE `type` = ".intval($typeId)."
					AND `zone` = ".intval($zoneId)."
					AND `weight` = '".floatval($weight)."'";
			$result = $this->db->fetchOne($sql);
			return !empty($result) ? true : false;
		}
		return true;
	}
	
	
	
	
	
	
	
	
	public function addShipping($array = null) {
		if (!empty($array)) {
			$array['type'] = intval($array['type']);
			$array['zone'] = intval($array['zone']);
			$array['country'] = intval($array['country']);
			$array['weight'] = floatval($array['weight']);
			$array['cost'] = floatval($array['cost']);
			$this->db->prepareInsert($array);
			return $this->db->insert($this->_table);
		}
		return false;
	}
	
	
	
	
	public function getShippingByIdTypeZone($id = null, $typeId = null, $zoneId = null) {
		if (!empty($id) && !empty($typeId) && !empty($zoneId)) {
			$sql = "SELECT *
					FROM `{$this->_table}`
					WHERE `id` = ".intval($id)."
					AND `type` = ".intval($typeId)."
					AND `zone` = ".intval($zoneId);
			return $this->db->fetchOne($sql);
		}
	}
	
	
	
	
	
	
	
	
	
	public function removeShipping($id = null) {
		if (!empty($id)) {
			$sql = "DELETE FROM `{$this->_table}`
					WHERE `id` = ".intval($id);
			return $this->db->query($sql);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getShippingByTypeCountry($typeId = null, $countryId = null) {
		if (!empty($typeId) && !empty($countryId)) {
			$sql = "SELECT `s`.*,
					IF (
						(
							SELECT COUNT(`weight`)
							FROM `{$this->_table}`
							WHERE `type` = `s`.`type`
							AND `country` = `s`.`country`
							AND `weight` < `s`.`weight`
							ORDER BY `weight` DESC
							LIMIT 0, 1
						) > 0,
						(
							SELECT `weight`
							FROM `{$this->_table}`
							WHERE `type` = `s`.`type`
							AND `country` = `s`.`country`
							AND `weight` < `s`.`weight`
							ORDER BY `weight` DESC
							LIMIT 0, 1
						) + 0.01,
						0
					) AS `weight_from`
				FROM `{$this->_table}` `s`
				WHERE `s`.`type` = ".intval($typeId)."
				AND `s`.`country` = ".intval($countryId)."
				ORDER BY `s`.`weight` ASC";
			return $this->db->fetchAll($sql);
		}
	}
	
	
	
	
	
	
	
	
	public function isDuplicateInternational($typeId = null, $countryId = null, $weight = null) {
		if (!empty($typeId) && !empty($countryId) && !empty($weight)) {
			$sql = "SELECT *
					FROM `{$this->_table}`
					WHERE `type` = ".intval($typeId)."
					AND `country` = ".intval($countryId)."
					AND `weight` = ".floatval($weight);
			$result = $this->db->fetchOne($sql);
			return !empty($result) ? true : false;
		}
		return true;
	}
	
	
	
	
	
	
	
	
	
	
	public function getShippingByIdTypeCountry($id = null, $typeId = null, $countryId = null) {
		if (!empty($id) && !empty($typeId) && !empty($countryId)) {
			$sql = "SELECT *
					FROM `{$this->_table}`
					WHERE `id` = ".intval($id)."
					AND `type` = ".intval($typeId)."
					AND `country` = ".intval($countryId);
			return $this->db->fetchOne($sql);
		}
	}
	
	
	
	
	
	
	
	
	public function addZone($array = null) {
		if (!empty($array)) {
			$this->db->prepareInsert($array);
			return $this->db->insert($this->_table_3);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	public function removeZone($id = null) {
		if (!empty($id)) {
			$sql = "DELETE FROM `{$this->_table_3}`
					WHERE `id` = ".intval($id);
			return $this->db->query($sql);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	public function updateZone($array = null, $id = null) {
		if (!empty($array) && !empty($id)) {
			$this->db->prepareUpdate($array);
			return $this->db->update($this->_table_3, $id);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	public function getPostCode($id = null, $zoneId = null) {
		if (!empty($id) && !empty($zoneId)) {
			$sql = "SELECT *
					FROM `{$this->_table_4}`
					WHERE `id` = ".intval($id)."
					AND `zone` = ".intval($zoneId);
			return $this->db->fetchOne($sql);	
		}
	}
	
	
	
	
	
	
	
	
	
	public function getPostCodes($zoneId = null) {
		if (!empty($zoneId)) {
			$sql = "SELECT *
					FROM `{$this->_table_4}`
					WHERE `zone` = ".intval($zoneId)."
					ORDER BY `post_code` ASC";
			return $this->db->fetchAll($sql);
		}
	}
	
	
	
	
	
	
	
	
	public function isDuplicatePostCode($postCode = null) {
		if (!empty($postCode)) {
			$sql = "SELECT *
					FROM `{$this->_table_4}`
					WHERE `post_code` = '".$this->db->escape($postCode)."'";
			$result = $this->db->fetchOne($sql);
			return !empty($result) ? true : false;
		}
		return true;
	}
	
	
	
	
	
	
	
	
	public function addPostCode($array = null) {
		if (!empty($array)) {
			$this->db->prepareInsert($array);
			return $this->db->insert($this->_table_4);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	public function removePostCode($id = null) {
		if (!empty($id)) {
			$sql = "DELETE FROM `{$this->_table_4}`
					WHERE `id` = ".intval($id);
			return $this->db->query($sql);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	public function getShippingOptions($user = null) {
		
		if (!empty($user)) {
			
			$weight = $this->objBasket->_weight;
			
			if (
				($user['same_address'] == 1 && $user['country'] == COUNTRY_LOCAL) ||
				($user['same_address'] == 0 && $user['ship_country'] == COUNTRY_LOCAL)
			) {
			
				$postCode = $user['same_address'] == 1 ? $user['post_code'] : $user['ship_post_code'];
				
				$postCode = strtoupper(Helper::alphaNumericalOnly($postCode));
				$zone = $this->getZone($postCode);
				
				// if zone not found return null
				if (empty($zone)) {
					return null;
				}
				
				$zoneId = $zone['zone'];
				
				$sql = "SELECT `t`.*,
						IF (
							{$weight} > (
								SELECT MAX(`weight`)
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `zone` = {$zoneId}
							),
							(
								SELECT `cost`
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `zone` = {$zoneId}
								ORDER BY `weight` DESC
								LIMIT 0, 1
							),
							(
								SELECT `cost`
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `zone` = {$zoneId}
								AND `weight` >= {$weight}
								ORDER BY `weight` ASC
								LIMIT 0, 1
							)
						) AS `cost`
						FROM `{$this->_table_2}` `t`
						WHERE `t`.`local` = 1
						AND `t`.`active` = 1
						ORDER BY `t`.`order` ASC";
						
				return $this->db->fetchAll($sql);
				
				
			} else {
				
				$countryId = $user['same_address'] == 1 ? $user['country'] : $user['ship_country'];
				
				$sql = "SELECT `t`.*,
						IF (
							{$weight} > (
								SELECT MAX(`weight`)
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `country` = {$countryId}
							),
							(
								SELECT `cost`
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `country` = {$countryId}
								ORDER BY `weight` DESC
								LIMIT 0, 1
							),
							(
								SELECT `cost`
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `country` = {$countryId}
								AND `weight` >= {$weight}
								ORDER BY `weight` ASC
								LIMIT 0, 1
							)
						) AS `cost`
						FROM `{$this->_table_2}` `t`
						WHERE `t`.`local` = 0
						AND `t`.`active` = 1
						ORDER BY `t`.`order` ASC";
						
				return $this->db->fetchAll($sql);
				
			}
			
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	public function getZone($postCode = null, $strLen = 4) {
		if (!empty($postCode)) {
			$pc = substr($postCode, 0, $strLen);
			$sql = "SELECT *
					FROM `{$this->_table_4}`
					WHERE `post_code` = '".$this->db->escape($pc)."'
					LIMIT 0, 1";
			$result = $this->db->fetchOne($sql);
			if (empty($result) && $strLen > 1) {
				$strLen--;
				return $this->getZone($postCode, $strLen);
			} else {
				return $result;
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	public function getDefault($user = null) {
		
		if (!empty($user)) {
			
			$countryId = $user['same_address'] == 1 ? $user['country'] : $user['ship_country'];
			
			if ($countryId == COUNTRY_LOCAL) {
				
				$sql = "SELECT `t`.*
						FROM `{$this->_table_2}` `t`
						WHERE `t`.`local` = 1
						AND `t`.`active` = 1
						AND `t`.`default` = 1";
						
				return $this->db->fetchOne($sql);
				
			} else {
				
				$sql = "SELECT `t`.*
						FROM `{$this->_table_2}` `t`
						WHERE `t`.`local` = 0
						AND `t`.`active` = 1
						AND `t`.`default` = 1";
						
				return $this->db->fetchOne($sql);
				
			}
			
		}
		
	}
	
	
	
	
	
	
	
	public function getShipping($user = null, $shippingId = null) {
		
		if (!empty($user) && !empty($shippingId)) {
			
			$weight = $this->objBasket->_weight;
			
			if (
				($user['same_address'] == 1 && $user['country'] == COUNTRY_LOCAL) ||
				($user['same_address'] == 0 && $user['ship_country'] == COUNTRY_LOCAL)
			) {
			
				$postCode = $user['same_address'] == 1 ? $user['post_code'] : $user['ship_post_code'];
				
				$postCode = strtoupper(Helper::alphaNumericalOnly($postCode));
				$zone = $this->getZone($postCode);
				
				// if zone not found return null
				if (empty($zone)) {
					return null;
				}
				
				$zoneId = $zone['zone'];
				
				$sql = "SELECT `t`.*,
						IF (
							{$weight} > (
								SELECT MAX(`weight`)
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `zone` = {$zoneId}
							),
							(
								SELECT `cost`
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `zone` = {$zoneId}
								ORDER BY `weight` DESC
								LIMIT 0, 1
							),
							(
								SELECT `cost`
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `zone` = {$zoneId}
								AND `weight` >= {$weight}
								ORDER BY `weight` ASC
								LIMIT 0, 1
							)
						) AS `cost`
						FROM `{$this->_table_2}` `t`
						WHERE `t`.`local` = 1
						AND `t`.`active` = 1
						AND `t`.`id` = {$shippingId}";
						
				return $this->db->fetchOne($sql);
				
				
			} else {
				
				$countryId = $user['same_address'] == 1 ? $user['country'] : $user['ship_country'];
				
				$sql = "SELECT `t`.*,
						IF (
							{$weight} > (
								SELECT MAX(`weight`)
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `country` = {$countryId}
							),
							(
								SELECT `cost`
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `country` = {$countryId}
								ORDER BY `weight` DESC
								LIMIT 0, 1
							),
							(
								SELECT `cost`
								FROM `{$this->_table}`
								WHERE `type` = `t`.`id`
								AND `country` = {$countryId}
								AND `weight` >= {$weight}
								ORDER BY `weight` ASC
								LIMIT 0, 1
							)
						) AS `cost`
						FROM `{$this->_table_2}` `t`
						WHERE `t`.`local` = 0
						AND `t`.`active` = 1
						AND `t`.`id` = {$shippingId}";
						
				return $this->db->fetchOne($sql);
				
			}
			
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}


















