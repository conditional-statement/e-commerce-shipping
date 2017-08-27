<?php
require_once("../inc/config.php");
$objBasket = is_object($objBasket) ? $objBasket : new Basket();
$out = array();
$out['bl_ti'] = $objBasket->_number_of_items;
$out['bl_st'] = number_format($objBasket->_sub_total, 2);
echo Helper::json($out);