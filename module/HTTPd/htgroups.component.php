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
 * LIB211 HTTPd_htgroups
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPd_htgroups extends LIB211Base {

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
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211HTTPd_htgroups')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211HTTPd_htgroupsException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211HTTPd_htgroups',time());
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
	 * Add entry to file
	 */
	public function add($file,$group,$user=NULL) {
		if (file_exists($file)) {
			$data = $this->read($file);
			if (isset($data[$group]) AND !in_array($user,$data[$group])) {
				if ($user === NULL) $data[$group] = array();
				else $data[$group][] = $user;
			} else {
				if ($user === NULL) $data[$group] = array();
				else $data[$group][] = $user;
			}
			krsort($data);
			return $this->write($file,$data);
		} else return FALSE;
	}

	/**
	 * Delete entry from file
	 */
	public function del($file,$group,$user=NULL) {
		if (file_exists($file)) {
			$data = $this->read($file);
			if (isset($data[$group])) {
				if ($user === NULL) unset($data[$group]);
				else {
					foreach($data[$group] as $k => $v) {
						if ($v == $user) unset($data[$group][$k]);
					}
				}
			}
			krsort($data);
			return $this->write($file,$data);
		} else return FALSE;
	}
	
	/**
	 * Read file
	 */
	public function read($file) {
		if (!$data = file($file)) return array();
		$pat_line = '/^([\x21-\x7E\x80-\xFF]{3,})\:\x20([\x20-\x7E\x80-\xFF]*)?$/';
		$pat_line_empty = '/^([\x21-\x7E\x80-\xFF]{3,})\:\x20?$/';
		$matches = array();
		$output = array();
		foreach($data as $value) {
			if (preg_match($pat_line,trim($value),$matches)) {
				$users = explode(' ',$matches[2]);
				sort($users);
				$output[$matches[1]] = $users;
			} elseif (preg_match($pat_line_empty,trim($value),$matches)) {
				$output[$matches[1]] = array();
			}
		}
		foreach ($output as $group => $data) $output[$group] = array_unique($data);
		ksort($output);
		return $output;
	}
	
	/**
	 * Write file
	 */
	public function write($file,$data) {
		$filedata = '';
		$i = 0;
		if (empty($data)) { @unlink($file); @touch($file); return TRUE; }
		if (!$handler = fopen($file,'w')) return FALSE;
		$counter = count($data);
		ksort($data);
		foreach($data as $key => $value) {
			$i++;
			$filedata .= $key.': ';
			$counter2 = count($value);
			sort($value);
			$k = 0;
			foreach($value as $subvalue) {
				$k++;
				if ($k == $counter2) $filedata .= $subvalue;
				else $filedata .= $subvalue.' ';
			}
			if ($i != $counter) $filedata .= EOL;
		}
		if (!fwrite($handler,$filedata,strlen($filedata))) return FALSE;
		@fclose($handler);
		return TRUE;
	}
}

/**
 * LIB211 HTTPd_htgroups Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPd_htgroupsException extends LIB211BaseException {
}