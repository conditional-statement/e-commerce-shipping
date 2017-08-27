<?php

if ($objShipping->removeZone($zone['id'])) {
	
	echo Helper::json(array('error' => false));
	
} else {
	throw new Exception('Record could not be removed');
}