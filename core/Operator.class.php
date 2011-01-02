<?php

if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

/**
 * LIB211 (C!$C0^211's php library)
 * 
 * Use at your own risk!
 * @author C!$C0^211
 *
 */
class LIB211 extends LIB211Base {
	
	/**
	 * LIB211 creation date
	 * @var integer
	 */
	private static $date_from = 1195599600;
	
	/**
	 * LIB211 edit date (will be inserted automatically)
	 * @var integer
	 */
	private static $date_to = 0;
	
	/**
	 * LIB211 build number
	 * @var integer
	 *
	 */
	private static $build = 70;
	
	/**
	 * LIB211 codename
	 * @var string
	 */
	private static $codename = 'Multiplicity';
	
	/**
	 * Instance counter
	 * @var integer
	 */
	private static $instances = 0;
	
	/**
	 * Client ip
	 * @var string
	 */
	private static $ip_client = '0.0.0.0';
		
	/**
	 * Enter description here ...
	 * @var integer
	 */
	private static $time_diff = 0;
	
	/**
	 * Enter description here ...
	 * @var integer
	 */
	private static $time_start = 0;
	
	/**
	 * Enter description here ...
	 * @var integer
	 */
	private static $time_stop = 0;
	
	/**
	 * LIB211 version
	 * @var string
	 */
	private static $version = '0.700';
	
	/**
	 * Counter for currently loaded objects
	 * @var integer
	 */
	private static $loaded = 0;
	
	/**
	 * Counter for created objects
	 * @var integer
	 */
	private static $created = 0;
	
	/**
	 * Counter for killed objects
	 * @var integer
	 */
	private static $killed = 0;
	
	/**
	 * List of object names
	 * @var array
	 */
	private static $objects = array();
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/lib211.lock')) {
			$this->__check('c','LIB211Exception');
			$this->__check('f','getenv');
			$this->__check('f','filemtime');
			$this->__check('f','microtime');
			$this->__check('f','round');
			$this->__check('v','_SERVER');
			touch(LIB211_ROOT.'/lib211.lock',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);
		self::$date_to = filemtime(__FILE__);
		if (isset($_SERVER["REMOTE_ADDR"])) self::$ip_client = $_SERVER["REMOTE_ADDR"];
		else self::$ip_client = @getenv("REMOTE_ADDR");
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
		$result["birth"] = self::$date_from;
		$result["clientip"] = self::$ip_client;
		$result["edit"] = self::$date_to;
		$result["build"] = self::$build;
		$result["codename"] = self::$codename;
		$result["instance"] = self::$instances;
		$result["runtime"] = self::$time_diff;
		$result["version"] = self::$version;
		$result["created"] = self::$created;
		$result["killed"] = self::$killed;
		$result["loaded"] = self::$loaded;
		$result["objects"] = self::$objects;
		return $result;
	}

	/**
	 * Return formatted info about LIB211
	 * @param mixed $mode
	 * @param mixed $dateformat
	 * @param mixed $break
	 * @return string
	 */
	public function info($mode,$dateformat="U",$break=EOL) {
		switch($mode) {
			case 'array':
				$output = array();
				$output['version'] = self::$version;
				$output['birth'] = date($dateformat,self::$date_from);
				$output['edit'] = date($dateformat,self::$date_to);
				$output['author'] = 'C!$C0^211';
				$output['codename'] = self::$codename;
			break;
			case 'string': default:
				$output =
					'Version = '.self::$version.$break.
					'Birth = '.date($dateformat,self::$date_from).$break.
					'Edit = '.date($dateformat,self::$date_to).$break.
					'Author = C!$C0^211';
			break;
			case 'editstring':
				$output =
					'Edit v'.self::$version.' from '.date($dateformat,self::$date_from).' to '.
					date($dateformat,self::$date_to).' by C!$C0^211';
			break;
		}
		return $output;
	}

	/**
	 * Check for existance of an object
	 * @param mixed $name
	 * @return boolean
	 */
	public function is_created($name) {
		if (isset($this->$name) AND is_object($this->$name)) return TRUE;
		else return FALSE;
	}

	
}


/**
 * LIB211 Exception
 * @author C!$C0^211
 *
 */
class LIB211Exception extends LIB211BaseException {
}