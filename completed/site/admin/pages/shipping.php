<?php
Login::restrictAdmin();

$objShipping = new Shipping();

$id = $this->objUrl->get('id');
$action = $this->objUrl->get('action');

try {
	
	switch($action) {
		
		case 'default':
		case 'active':
		case 'remove':
		case 'update':
		case 'duplicate':
		case 'rates':
		if (!empty($id)) {
			
			$type = $objShipping->getType($id);
			if (!empty($type)) {
				
				switch($action) {
					
					case 'default':
					require_once('shipping/default.php');
					break;
					case 'active':
					require_once('shipping/active.php');
					break;
					case 'remove':
					require_once('shipping/remove.php');
					break;
					case 'update':
					require_once('shipping/update.php');
					break;
					case 'duplicate':
					require_once('shipping/duplicate.php');
					break;
					case 'rates':
					require_once('shipping/rates.php');
					break;
					
				}
				
			} else {
				throw new Exception('Record not found');
			}
			
		} else {
			throw new Exception('Missing parameter');
		}
		break;
		
		case 'sort':
		require_once('shipping/sort.php');
		break;
		
		case 'add':
		require_once('shipping/add.php');
		break;
		
		default:
		require_once('shipping/list.php');
		
	}
	
} catch (Exception $e) {
	
	echo Helper::json(array(
		'error' => true,
		'message' => $e->getMessage()
	));
	
}