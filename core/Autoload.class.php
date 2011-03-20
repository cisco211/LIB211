<?php 

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
}

/**
 * LIB211 Autloader
 * 
 * @author C!$C0^211
 *
 */
class LIB211Autoload extends LIB211Base {

	/**
	 * Loader object
	 * @staticvar LIB211Autoload
	 */
	public static $loader;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		spl_autoload_register(array($this,'module'));
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		parent::__destruct(); 
	}
	
	/**
	 * Initializator
	 * @return LIB211Autoload
	 */
	public static function __init() {
		if (self::$loader == NULL) self::$loader = new self();
		return self::$loader;
	}
	
	/**
	 * Add LIB211 modules to autoloader
	 * @param string $name
	 */
	public function module($name) {
		$module = str_replace('LIB211','',$name);
		set_include_path(LIB211_ROOT.'/module/'.$module);
		spl_autoload_extensions('.class.php');
		spl_autoload($module);
	}

}

/**
 * LIB211 Autoload Exception
 * 
 * @author C!$C0^211
 *
 */
class LIB211AutoloadException extends LIB211BaseException {
}

/**
 * Call a autoloadable function
 * @throws LIB211BaseException
 * @return mixed
 */
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

// Initialize autoloader
if (LIB211_AUTOLOAD === TRUE) LIB211Autoload::__init();