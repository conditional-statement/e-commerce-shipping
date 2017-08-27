<?php

$objDb = new Dbase();

$array = array(
	
	1 => array(
		'weight' => 10.00,
		'cost' => 5.00
	),
	2 => array(
		'weight' => 30.00,
		'cost' => 8.00
	),
	3 => array(
		'weight' => 50.00,
		'cost' => 12.00
	),
	4 => array(
		'weight' => 100.00,
		'cost' => 15.00
	),
	5 => array(
		'weight' => 200.00,
		'cost' => 20.00
	)
	
);

foreach($array as $key => $row) {
	
	$sql = "SELECT *
			FROM `zones`
			ORDER BY `id` ASC";
	
	$zoneTypes = $objDb->fetchAll($sql);
	
	if (!empty($zoneTypes)) {
		
		foreach($zoneTypes as $ztRow) {		
		
			$row['cost'] = floatval($row['cost'] + 3);			
			
			$sql = "SELECT *
					FROM `shipping_type`
					ORDER BY `id` ASC";
			
			$shippingTypes = $objDb->fetchAll($sql);
			
			if (!empty($shippingTypes)) {
				
				$cost = $row['cost'];
				
				foreach($shippingTypes as $stRow) {
					
					$cost = floatval($cost - 1.5);
					
					$objDb->prepareInsert(array(
						'type' => $stRow['id'],
						'zone' => $ztRow['id'],
						'weight' => $row['weight'],
						'cost' => $cost
					));
					
					$objDb->insert('shipping_local');
					$objDb->_insert_keys = array();
					$objDb->_insert_values = array();
					
				}
				
			}	
		
		}
		
	}
	
	
}