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
 * LIB211 HTTPd
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPd extends LIB211Base {

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
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211HTTPd')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211HTTPdException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211HTTPd',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);
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
	
	/**
	 * Return new htconf component
	 * @return object
	 */
	public static function htconf() {
		require_once(LIB211_ROOT.'/module/HTTPd/htconf.component.php');
		return new LIB211HTTPd_htconf();
	}
	
	/**
	 * Return new htgroups component
	 * @return object
	 */
	public static function htgroups() {
		require_once(LIB211_ROOT.'/module/HTTPd/htgroups.component.php');
		return new LIB211HTTPd_htgroups();
	}
	
	/**
	 * Return new htpasswd component
	 * @return object
	 */
	public static function htpasswd() {
		require_once(LIB211_ROOT.'/module/HTTPd/htpasswd.component.php');
		return new LIB211HTTPd_htpasswd();
	}
	
}

/**
 * LIB211 Example Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPdException extends LIB211BaseException {
}