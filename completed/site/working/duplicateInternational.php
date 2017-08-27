<?php

$objDb = new Dbase();

$sql = "SELECT *
		FROM `shipping`
		WHERE `type` = 5";
		
$list = $objDb->fetchAll($sql);

if (!empty($list)) {
	
	foreach($list as $row) {
	
		$cost = floatval($row['cost'] - 3);
					
		$objDb->prepareInsert(array(
			'type' => 10,
			'country' => $row['country'],
			'weight' => $row['weight'],
			'cost' => $cost
		));
		
		$objDb->insert('shipping');
		$objDb->_insert_keys = array();
		$objDb->_insert_values = array();
		
	}
	
}