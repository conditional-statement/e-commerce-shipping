<?php
class Autoloader {
	
	
	public static function load($className) {
		
		$class = str_replace('\\', DS, ltrim($className, '\\'));
		$class = str_replace('_', DS, $className).'.php';
		@require_once(CLASSES_PATH.DS.$class);
		
	}
	
	
	
}