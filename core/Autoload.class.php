<?php 

class LIB211Autoload extends LIB211Base {

	public static $loader;

	public function __construct() {
		parent::__construct(); 
		spl_autoload_register(array($this,'module'));
	}

	public function __destruct() {
		parent::__destruct(); 
	}
	
	public static function __init() {
		if (self::$loader == NULL) self::$loader = new self();
		return self::$loader;
	}
	
	public function module($name) {
		$module = str_replace('LIB211','',$name);
		set_include_path(LIB211_ROOT.'/module/'.$module);
		spl_autoload_extensions('.class.php');
		spl_autoload($module);
	}

}

class LIB211AutoloadException extends LIB211BaseException {
}

LIB211Autoload::__init();