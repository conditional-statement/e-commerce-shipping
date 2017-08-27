<?php

$objDb = new Dbase();

$array = array(
	
	1 => array(
		'weight' => 10.00,
		'cost' => 15.00
	),
	2 => array(
		'weight' => 30.00,
		'cost' => 18.00
	),
	3 => array(
		'weight' => 50.00,
		'cost' => 22.00
	),
	4 => array(
		'weight' => 100.00,
		'cost' => 35.00
	),
	5 => array(
		'weight' => 200.00,
		'cost' => 40.00
	)
	
);

foreach($array as $key => $row) {
	
	$sql = "SELECT *
			FROM `countries`
			ORDER BY `id` ASC";
	
	$countryTypes = $objDb->fetchAll($sql);
	
	if (!empty($countryTypes)) {
		
		foreach($countryTypes as $ctRow) {			
					
			$objDb->prepareInsert(array(
				'country' => $ctRow['id'],
				'weight' => $row['weight'],
				'cost' => $row['cost']
			));
			
			$objDb->insert('shipping_international');
			$objDb->_insert_keys = array();
			$objDb->_insert_values = array();
		
		}
		
	}
	
	
}