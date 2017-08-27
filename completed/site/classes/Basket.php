<?php
class Basket {
	
	public $_inst_catalogue;
	public $_empty_basket;
	public $_vat_rate;
	public $_number_of_items;
	public $_sub_total;
	public $_vat;
	public $_total;
	
	public $_weight;
	private $_array_weight;
	
	public $_final_shipping_type;
	public $_final_shipping_cost;
	public $_final_sub_total;
	public $_final_vat;
	public $_final_total;
	
	public $_user;
	
	
	
	
	public function __construct($user = null) {
	
		if (!empty($user)) {
			$this->_user = $user;
		}
	
		$this->_inst_catalogue = new Catalogue();
		$this->_empty_basket = empty($_SESSION['basket']) ? true : false;
		
		if (!empty($this->_user) && ($this->_user['country'] == COUNTRY_LOCAL || INTERNATIONAL_VAT)) {
			$objBusiness = new Business();
			$this->_vat_rate = $objBusiness->getVatRate();
		} else {
			$this->_vat_rate = 0;				
		}
		
		$this->noItems();
		$this->subtotal();
		$this->vat();
		$this->total();
		$this->process();
		
	}
	
	
	
	
	public function noItems() {
		$value = 0;
		if (!$this->_empty_basket) {
			foreach($_SESSION['basket'] as $key => $basket) {
				$value += $basket['qty'];
			}
		}
		$this->_number_of_items = $value;
	}
	
	
	
	
	public function subtotal() {
		$value = 0;
		if (!$this->_empty_basket) {
			foreach($_SESSION['basket'] as $key => $basket) {
				$product = $this->_inst_catalogue->getProduct($key);
				$value += ($basket['qty'] * $product['price']);
				$this->_array_weight[] = ($basket['qty'] * $product['weight']);
			}
		}
		$this->_weight = array_sum($this->_array_weight);
		$this->_sub_total = round($value, 2);
	}
	
	
	
	
	public function vat() {
		$value = 0;
		if (!$this->_empty_basket) {
			$value = ($this->_vat_rate * ($this->_sub_total / 100));
		}		
		$this->_vat = round($value, 2);	
	}
	
	
	
	
	public function total() {
		$this->_total = round(($this->_sub_total + $this->_vat), 2);	
	}
	
	
	

	public static function activeButton($sess_id) {
		if(isset($_SESSION['basket'][$sess_id])) {
			$id = 0;
			$label = "Remove from basket";
		} else {
			$id = 1;
			$label = "Add to basket";
		}
		
		$out  = "<a href=\"#\" class=\"add_to_basket";
		$out .= $id == 0 ? " red" : null;
		$out .= "\" rel=\"";
		$out .= $sess_id."_".$id;
		$out .= "\">{$label}</a>";
		return $out;
	}
	
	
	
	
	
	public function itemTotal($price = null, $qty = null) {
		if (!empty($price) && !empty($qty)) {
			return round(($price * $qty), 2);
		}
	}
	
	
	
	
	
	public static function removeButton($id = null) {
		if (!empty($id)) {
			if (isset($_SESSION['basket'][$id])) {
				$out  = "<a href=\"#\" class=\"remove_basket red";
				$out .= "\" rel=\"{$id}\">Remove</a>";
				return $out;
			}
		}
	}
	
	
	private function process() {
	
		$this->_final_shipping_type = Session::getSession('shipping_type');
		$this->_final_shipping_cost = Session::getSession('shipping_cost');
		$this->_final_sub_total = round(($this->_sub_total + $this->_final_shipping_cost), 2);
		$this->_final_vat = round(($this->_vat_rate * ($this->_final_sub_total / 100)), 2);
		$this->_final_total = round(($this->_final_sub_total + $this->_final_vat), 2);
		
	}
	
	
	
	public function addShipping($shipping = null) {
		if (!empty($shipping)) {
			Session::setSession('shipping_id', $shipping['id']);
			Session::setSession('shipping_cost', $shipping['cost']);
			Session::setSession('shipping_type', $shipping['name']);
			$this->process();
			return true;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	public function clearShipping() {
		
		Session::clear('shipping_id');
		Session::clear('shipping_cost');
		Session::clear('shipping_type');
		
		$this->_final_shipping_type = null;
		$this->_final_shipping_cost = null;
		$this->_final_sub_total = null;
		$this->_final_vat = null;
		$this->_final_total = null;
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}









