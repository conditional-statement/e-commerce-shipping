<?php
require_once('../inc/config.php');

try {
	
	if (!empty($_GET['shipping'])) {
		
		Login::restrictFront();
		
		$objUser = new User();
		$user = $objUser->getUser(Session::getSession(Login::$_login_front));
		
		if (!empty($user)) {
			
			$objBasket = new Basket($user);
			
			$objShipping = new Shipping($objBasket);
			
			$shippingSelected = $objShipping->getShipping($user, $_GET['shipping']);
			
			if (!empty($shippingSelected)) {
				
				if ($objBasket->addShipping($shippingSelected)) {
					
					$out = array();
					
					$out['basketSubTotal'] = '&pound;'.number_format($objBasket->_final_sub_total, 2);
					$out['basketVat'] = '&pound;'.number_format($objBasket->_final_vat, 2);
					$out['basketTotal'] = '&pound;'.number_format($objBasket->_final_total, 2);
					
					echo Helper::json(array('error' => false, 'totals' => $out));
					
				} else {
					throw new Exception('Shipping could not be added');
				}
				
			} else {
				throw new Exception('Shipping could not be found');
			}
			
		} else {
			throw new Exception('User could not be found');
		}
		
	} else {
		throw new Exception('Invalid request');
	}
	
} catch (Exception $e) {
	echo Helper::json(array('error' => true, 'message' => $e->getMessage()));
}




