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
 * LIB211 HTTPd_htpasswd
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPd_htpasswd extends LIB211Base {

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
	 * Current used algorithm
	 */
	private $algorithm = 'crypt';
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211HTTPd_htpasswd')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211HTTPd_htpasswdException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211HTTPd_htpasswd',time());
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
	 * Add user to file
	 */
	public function add($file,$user,$passwd,$mode=NULL) {
		if (file_exists($file)) {
			$types = array('crypt','md5','none');
			if ($mode == NULL) $mode = $this->algorithm;
			if (!in_array($mode,$types)) $mode = 'crypt';
			$passwd = $this->hash($passwd,$mode);
			$input = $this->read($file);
			foreach($input as $key => $value) if ($key != $user) $data[$key] = $value;
			$data[$user] = $passwd;
			ksort($data);
			return $this->write($file,$data,'none');
		} else return FALSE;
	}
	
	/**
	 * Set algorithm
	 */
	public function algorithm($type='') {
		$types = array('crypt','md5','none');
		if (in_array($type,$types)) $this->algorithm = $type;
		else $this->algorithm = 'crypt';
		return TRUE;
	}
	
	/**
	 * Delete user in file
	 */
	public function del($file,$user) {
		$data = array();
		if (file_exists($file)) {
			$input = $this->read($file);
			foreach($input as $key => $value) if ($key != $user) $data[$key] = $value;
			ksort($data);
			return $this->write($file,$data,'none');
		} else return FALSE;
	}
	
	/**
	 * Hash password
	 */
	public function hash($passwd,$mode=NULL) {
		$types = array('crypt','md5','none');
		$tmp = NULL;
		if ($mode == NULL) $mode = $this->algorithm;
		if (!in_array($mode,$types)) $mode = 'crypt';
		switch($mode) {
			case 'crypt': return crypt($passwd); break;
			case 'md5':
				//@require_once(LIB211_ROOT.'/shared/md5apr1.php');
				if (!function_exists('md5apr1')) return $passwd;
				return md5apr1($passwd);
			break;
			case 'none': default: return $passwd; break;
		}
	}
	
	/**
	 * Read file
	 */
	public function read($file) {
		if (!$data = file($file)) return array();
		$pat_line = '/^([\x21-\x7E\x80-\xFF]{3,})\:([\x20-\x39\x3B-\x7E\x80-\xFF]*)?$/';
		$matches = array();
		$output = array();
		foreach($data as $value) {
			if (preg_match($pat_line,trim($value),$matches)) $output[$matches[1]] = $matches[2];
		}
		ksort($output);
		return $output;
	}
	
	/**
	 * Write file
	 */
	public function write($file,$data,$mode=NULL) {
		$filedata = '';
		$types = array('crypt','md5','none');
		$i = 0;
		if (empty($data)) { @unlink($file); @touch($file); return TRUE; }
		if ($mode == NULL) $mode = $this->algorithm;
		if (!in_array($mode,$types)) $mode = 'crypt';
		if (!$handler = fopen($file,'w')) return FALSE;
		$counter = count($data);
		ksort($data);
		foreach($data as $key => $value) {
			$i++;
			if ($i == $counter) $filedata .= $key.':'.$this->hash($value,$mode);
			else $filedata .= $key.':'.$this->hash($value,$mode).EOL;
		}
		if (!fwrite($handler,$filedata,strlen($filedata))) return FALSE;
		@fclose($handler);
		return TRUE;
	}
	
}

/**
 * LIB211 HTTPd_htpasswd Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPd_htpasswdException extends LIB211BaseException {
}