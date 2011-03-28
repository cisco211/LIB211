<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

/**
 * LIB211 Scoper (based on the researches of HMH211)
 * This class is experimental. Use at your own risk, because Scoper allows unusual concepts!
 * 
 * @author C!$C0^211
 *
 */
class LIB211Scoper extends LIB211Base {

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
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Scoper')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211ScoperException');
			$this->__check('f','in_array');
			$this->__check('f','is_array');
			$this->__check('f','file_exists');
			$this->__check('f','func_get_arg');
			$this->__check('f','func_num_args');
			$this->__check('f','ob_end_clean');
			$this->__check('f','ob_get_contents');
			$this->__check('f','ob_start');
			$this->__check('v','GLOBALS');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Scoper',time());
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
		$result["instance"] = self::$instances;
		$result["runtime"] = self::$time_diff;
		return $result;
	}

	/**
	 * Get an object property
	 * @param mixed $parameter
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($parameter,$default=NULL) {
		if (isset($this->$parameter)) return $this->$parameter;
		else return $default;
	}

	/**
	 * Include file and return preprocessed output
	 * @param mixed $file
	 * @throws LIB211ScoperException
	 * @return string
	 */
	public function run($file=NULL) {
		if (!func_get_arg(0))
			throw new LIB211ScoperException('No include file specified.');
		if (!file_exists(func_get_arg(0)))
			throw new LIB211ScoperException('Include file "'.func_get_arg(0).'" does not exist.');
		for ($___i = 1; $___i < func_num_args(); $___i++) {
			if (func_get_arg($___i) == ':all:') {
				foreach ($GLOBALS as $___k => $___v) {
					if (!in_array($___k,array('GLOBALS','_POST','_GET','_COOKIE','_FILES','_REQUEST','_ENV'))) {
						$___x = $___k;
						$$___x = $___v;
					}
				}
			}
			elseif (is_array(func_get_arg($___i))) {
				foreach(func_get_arg($___i) as $___x) {
					if (!isset($GLOBALS[$___x])) continue;
					$$___x = $GLOBALS[$___x];
				}
			}
			else {
				$___x = func_get_arg($___i);
				if (!isset($GLOBALS[$___x])) continue;
				$$___x = $GLOBALS[$___x];
			}
		}
		ob_start();
		include(func_get_arg(0));
		$buffer = ob_get_contents();
		ob_clean();
		return $buffer;
	}

	/**
	 * Set object property
	 * @param mixed $parameter
	 * @param mixed $value
	 */
	public function set($parameter,$value) {
		$this->$parameter = $value;
	}

}

/**
 * LIB211 Scoper Exception
 * 
 * @author C!$C0^211
 *
 */
class LIB211ScoperException extends LIB211BaseException {
}