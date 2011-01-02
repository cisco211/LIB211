<?php 

if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

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

function lib211() {
	$argv = func_get_args();
	$function = $argv[0];
	$args = array_slice($argv,1);
	if (function_exists($function)) {
		$return = call_user_func_array($function,$args);
	}
	else {
		$path = LIB211_ROOT.'/function/'.$function.'.function.php';
		require_once($path);
		if (function_exists($function)) {
			$return = call_user_func_array($function,$args);
		}
		else {
			throw new LIB211BaseException('Missing function "'.$function.'".');
		}
	}
	return $return;
}

LIB211Autoload::__init();