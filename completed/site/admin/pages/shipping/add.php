<?php

$objForm = new Form();
$objValid = new Validation($objForm);
$objValid->_expected = array('name', 'local');
$objValid->_required = array('name');

try {
	
	if ($objValid->isValid()) {
		
		if ($objShipping->addType($objValid->_post)) {
			
			$replace = array();
			$urlSort = $this->objUrl->getCurrent(array('action', 'id'), false, array(
				'action', 'sort'
			));
			
			if (!empty($objValid->_post['local'])) {
				
				$rows = $objShipping->getTypes(1);
				$zones = $objShipping->getZones();
				$replace['#typesLocal'] = Plugin::get('admin'.DS.'shipping', array(
					'rows' => $rows,
					'objUrl' => $this->objUrl,
					'urlSort' => $urlSort,
					'zones' => $zones
				));
				
			} else {
				
				$rows = $objShipping->getTypes();
				$objCountry = new Country();
				$countries = $objCountry->getAllExceptLocal();
				$replace['#typesInternational'] = Plugin::get('admin'.DS.'shipping', array(
					'rows' => $rows,
					'objUrl' => $this->objUrl,
					'urlSort' => $urlSort,
					'countries' => $countries
				));
				
			}
			
			echo Helper::json(array('error' => false, 'replace' => $replace));
			
		} else {
			$objValid->add2Errors('name', 'Record could not be added');
			throw new Exception('Record could not be added');
		}
		
	} else {
		throw new Exception('Missing parameter');
	}
	
} catch (Exception $e) {
	
	echo Helper::json(array('error' => true, 'validation' => $objValid->_errorsMessages));
	
}



