<?php

$objForm = new Form();
$objValid = new Validation($objForm);
$objValid->_expected = array('weight', 'cost');
$objValid->_required = array('weight', 'cost');

try {
	
	if ($objValid->isValid()) {
	
		if ($objShipping->isDuplicateLocal($id, $zid, $objValid->_post['weight'])) {
			$objValid->add2Errors('weight', 'Duplicate weight');
			throw new Exception('Duplicate weight');
		}
		
		$objValid->_post['type'] = $id;
		$objValid->_post['zone'] = $zid;
		$objValid->_post['country'] = COUNTRY_LOCAL;
		
		if ($objShipping->addShipping($objValid->_post)) {
			
			$replace = array();
			
			$shipping = $objShipping->getShippingByTypeZone($id, $zid);
			
			$replace['#shippingList'] = Plugin::get('admin'.DS.'shipping-cost', array(
				'rows' => $shipping,
				'objUrl' => $this->objUrl
			));
			
			echo Helper::json(array('error' => false, 'replace' => $replace));
			
		} else {
			$objValid->add2Errors('weight', 'Record could not be added');
			throw new Exception('Record could not be added');
		}
		
		
		
	} else {
		throw new Exception('Invalid request');
	}
	
} catch (Exception $e) {
	echo Helper::json(array('error' => true, 'validation' => $objValid->_errorsMessages));
}









