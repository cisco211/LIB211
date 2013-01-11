<?php
/**
 * @package LIB211
 */

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
}

/**
 * LIB211 Icon
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211Icon extends LIB211Base {

	/**
	 * Instance counter
	 * @staticvar integer
	 */
	private static $instances = 0;
	
	/**
	 * Runtime of object
	 * @staticvar float
	 */
	private static $time_diff = 0;
	
	/**
	 * Start time of object
	 * @staticvar float
	 */
	private static $time_start = 0;
	
	/**
	 * Stop time of object
	 * @staticvar float
	 */
	private static $time_stop = 0;

	private $bg = NULL; #Hintergrundfarbe
	private $fg = NULL; #Vordergrundfarbe
	private $ty = NULL; #Transparenz-Farbtyp
	private $iconpath = NULL; #Pfad zu den Icons
	private $apiurl = NULL; #URL zum API-Script
	private $api_bg = NULL; #Hintergrundfarben-Parameter
	private $api_fg = NULL; #Vordergrundfarben-Parameter
	private $api_in = NULL; #Icon-Parameter
	private $api_ty = NULL; #Transparenz-Parameter
	
	public $data = NULL;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Icon')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211IconException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Icon',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);
		$this->bg = 0xFFFFFF;
		$this->fg = 0x000000;
		$this->ty = "none";
		$this->iconpath = LIB211_ROOT."/module/Icon/icon";
		$this->apiurl = "Icon.php";
		$this->api_bg = "bg";
		$this->api_fg = "fg";
		$this->api_in = "in";
		$this->api_ty = "ty";
		$this->data = array();
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		parent::__destruct(); 
		self::$instances--;
	}

	/**
	 * Return object status
	 * @return array
	 */
	public function __status() {
		self::$time_stop = microtime(TRUE);
		self::$time_diff = round(self::$time_stop - self::$time_start,11);
		$result = array();
		$result['instance'] = self::$instances;
		$result['runtime'] = self::$time_diff;
		return $result;
	}
	
}

/**
 * LIB211 Icon Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211IconException extends LIB211BaseException {
}