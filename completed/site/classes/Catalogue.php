<?php
class Catalogue extends Application {

	private $_table = 'categories';
	private $_table_2 = 'products';
	public $_path = null;
	public static $_currency = '&pound;';
	
	
	
	
	
	
	
	
	
	public function __construct() {
		parent::__construct();
		$this->_path = DS.'media'.DS.'catalogue'.DS;
	}
	
	
	
	
	
	
	
	
	
	
	public function getCategoryByIdentity($identity = null) {
		if (!empty($identity)) {
			$sql = "SELECT * FROM `{$this->_table}`
					WHERE `identity` = '".$this->db->escape($identity)."'";
			return $this->db->fetchOne($sql);
		}
	}
	
	
	
	
	
	
	
	
	
	
	public function getProductByIdentity($identity = null) {
		if (!empty($identity)) {
			$sql = "SELECT * FROM `{$this->_table_2}`
					WHERE `identity` = '".$this->db->escape($identity)."'";
			return $this->db->fetchOne($sql);
		}
	}
	
	
	
	
	
	
	
	
	
	
	public function getCategories() {
		$sql = "SELECT * FROM `{$this->_table}`
				ORDER BY `name` ASC";
		return $this->db->fetchAll($sql);
	}
	
	
	
	
	
	
	
	
	public function getCategory($id = null) {
		if (!empty($id)) {
			$sql = "SELECT `c`.*,
					(
						SELECT COUNT(`id`)
						FROM `{$this->_table_2}`
						WHERE `category` = `c`.`id`
					) AS `products_count`
					FROM `{$this->_table}` `c`
					WHERE `c`.`id` = '".$this->db->escape($id)."'";
			return $this->db->fetchOne($sql);
		}
	}
	
	
	
	
	
	
	
	
	public function addCategory($array = null) {
		if (!empty($array) && is_array($array)) {
			$sql = "INSERT INTO `{$this->_table}`
					(
						`name`, 
						`identity`, 
						`meta_title`, 
						`meta_description`, 
						`meta_keywords`
					) 
					VALUES (
						'".$this->db->escape($array['name'])."',
						'".$this->db->escape($array['identity'])."',
						'".$this->db->escape($array['meta_title'])."',
						'".$this->db->escape($array['meta_description'])."',
						'".$this->db->escape($array['meta_keywords'])."'
					)";
			return $this->db->query($sql);
		}
	}
	
	
	
	
	
	
	
	
	
	public function updateCategory($array = null, $id = null) {
		if (!empty($array) && is_array($array) && !empty($id)) {
			$sql = "UPDATE `{$this->_table}`
					SET `name` = '".$this->db->escape($array['name'])."',
					`identity` = '".$this->db->escape($array['identity'])."',
					`meta_title` = '".$this->db->escape($array['meta_title'])."',
					`meta_description` = '".$this->db->escape($array['meta_description'])."',
					`meta_keywords` = '".$this->db->escape($array['meta_keywords'])."'
					WHERE `id` = '".$this->db->escape($id)."'";
			return $this->db->query($sql);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	public function duplicateCategory($name = null, $id = null) {
		if (!empty($name)) {
			$sql  = "SELECT * FROM `{$this->_table}`
					WHERE `name` = '".$this->db->escape($name)."'";
			$sql .= !empty($id) ? 
					" AND `id` != '".$this->db->escape($id)."'" : 
					null;
			return $this->db->fetchOne($sql);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	public function removeCategory($id = null) {
		if (!empty($id)) {
			$sql = "DELETE FROM `{$this->_table}`
					WHERE `id` = '".$this->db->escape($id)."'";
			$this->db->query($sql);
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	public function getProducts($cat) {
		$sql = "SELECT * FROM `{$this->_table_2}`
				WHERE `category` = '".$this->db->escape($cat)."'
				ORDER BY `date` DESC";
		return $this->db->fetchAll($sql);
	}
	
	
	
	
	
	
	
	
	
	public function getProduct($id) {
		$sql = "SELECT * FROM `{$this->_table_2}`
				WHERE `id` = '".$this->db->escape($id)."'";
		return $this->db->fetchOne($sql);
	}
	
	
	
	
	
	
	
	
	public function getAllProducts($srch = null) {
		$sql = "SELECT * FROM `{$this->_table_2}`";
		if (!empty($srch)) {
			$srch = $this->db->escape($srch);
			$sql .= " WHERE `name` LIKE '%{$srch}%' || `id` = '{$srch}'";
		}
		$sql .= " ORDER BY `date` DESC";
		return $this->db->fetchAll($sql);
	}
	
	
	
	
	
	public function addProduct($params = null) {
		if (!empty($params)) {
			$params['date'] = Helper::setDate();
			$this->db->prepareInsert($params);
			$out = $this->db->insert($this->_table_2);
			$this->_id = $this->db->_id;
			return $out;
		}
		return false;
	}
	
	
	
	
	
	
	
	public function updateProduct($params = null, $id = null) {
		if (!empty($params) && !empty($id)) {
			$this->db->prepareUpdate($params);
			return $this->db->update($this->_table_2, $id);
		}
	}
	
	
	
	
	
	
	
	
	public function removeProduct($id = null) {
		if (!empty($id)) {
			$product = $this->getProduct($id);
			if (!empty($product)) {
				if (is_file(CATALOGUE_PATH.DS.$product['image'])) {
					unlink(CATALOGUE_PATH.DS.$product['image']);
				}
				$sql = "DELETE FROM `{$this->_table_2}`
						WHERE `id` = '".$this->db->escape($id)."'";
				return $this->db->query($sql);
			}
			return false;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function isDuplicateProduct($identity = null, $id = null) {
		if (!empty($identity)) {
			$sql = "SELECT *
					FROM `{$this->_table_2}`
					WHERE `identity` = '".$this->db->escape($identity)."'";
			if (!empty($id)) {
				$sql .= " AND `id` != '".$this->db->escape($id)."'";
			}
			$result = $this->db->fetchAll($sql);
			return !empty($result) ? true : false;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	public function isDuplicateCategory($identity = null, $id = null) {
		if (!empty($identity)) {
			$sql = "SELECT *
					FROM `{$this->_table}`
					WHERE `identity` = '".$this->db->escape($identity)."'";
			if (!empty($id)) {
				$sql .= " AND `id` != '".$this->db->escape($id)."'";
			}
			$result = $this->db->fetchAll($sql);
			return !empty($result) ? true : false;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}