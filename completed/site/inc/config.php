<?php
if(!isset($_SESSION)) {
	session_start();
}

// local country id
defined("COUNTRY_LOCAL")
	|| define("COUNTRY_LOCAL", 229);

// whether the vat should apply to sales outside of the local country
defined("INTERNATIONAL_VAT")
	|| define("INTERNATIONAL_VAT", false);

defined("PAGE_EXT")
	|| define("PAGE_EXT", ".html");

// site domain name with http
defined("SITE_URL")
	|| define("SITE_URL", "http://".$_SERVER['SERVER_NAME']);
	
// directory separator
defined("DS")
	|| define("DS", DIRECTORY_SEPARATOR);

// root path
defined("ROOT_PATH")
	|| define("ROOT_PATH", realpath(dirname(__FILE__) . DS."..".DS));
	
// classes folder
defined("CLASSES_DIR")
	|| define("CLASSES_DIR", "classes");
	
// classes path
defined("CLASSES_PATH")
	|| define("CLASSES_PATH", ROOT_PATH.DS.CLASSES_DIR);

// plugin path
defined("PLUGIN_PATH")
	|| define("PLUGIN_PATH", ROOT_PATH.DS."plugin");

// pages directory
defined("PAGES_DIR")
	|| define("PAGES_DIR", "pages");

// modules folder
defined("MOD_DIR")
	|| define("MOD_DIR", "mod");
	
// inc folder
defined("INC_DIR")
	|| define("INC_DIR", "inc");
	
// templates folder
defined("TEMPLATE_DIR")
	|| define("TEMPLATE_DIR", "template");
	
// emails path
defined("EMAILS_PATH")
	|| define("EMAILS_PATH", ROOT_PATH.DS."emails");
	
// catalogue images path
defined("CATALOGUE_PATH")
	|| define("CATALOGUE_PATH", ROOT_PATH.DS."media".DS."catalogue");
	
// add all above directories to the include path
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(ROOT_PATH.DS.MOD_DIR),
	realpath(ROOT_PATH.DS.INC_DIR),
	get_include_path()
)));

require_once(CLASSES_PATH.DS.'Autoloader.php');

spl_autoload_register(array('Autoloader', 'load'));









