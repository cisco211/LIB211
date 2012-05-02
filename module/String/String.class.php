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
 * LIB211 String
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211String extends LIB211Base {

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
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211String')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211StringException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211String',time());
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
	 * Returns a given size in byte as best human readable string
	 * @param mixed $size
	 * @param boolean $reverse
	 * @param boolean $fake
	 * @return string
	 */
	public function byte2human($size,$reverse=FALSE,$fake=FALSE) {
		if (is_string($size)) $size = count(str_split($size));
		$factor = 1024;
		$i = 0;
		$units = array('Byte','KByte','MByte','GByte','TByte','PByte','EByte','ZByte','YByte');
		$units_count = count($units) -1;
		if($fake === TRUE) $factor = 1000;
		while ($size >= $factor AND $i < $units_count) {
			if ($reverse === TRUE) $size *= $factor;
			else $size /= $factor;
			$i++;
		}
		return sprintf('%01.2f',$size).' '.$units[$i];
	}

	/**
	 * Fill string to a given size in any direction and any fill characters
	 * @param string $string
	 * @param integer $size
	 * @param string $dir
	 * @param string $chr
	 * @return string
	 */
	public function fill($string,$size,$dir='l',$chr='0') {
		$len = strlen($string);
		for($i = $len; $i < $size; $i++) {
			if ($dir === 'r') $string = $string.$chr;
			else $string = $chr.$string;
		}
		return $string;
	}
	
	/**
	 * Find a string
	 * @param string $needle
	 * @param string $haystack
	 * @param integer $offset
	 * @param boolean $case
	 * @return boolean
	 */
	public function find($needle,$haystack,$offset=0,$case=FALSE) {
		if ($case = TRUE) {
			$haystack = strtolower($haystack);
			$needle = strtolower($needle);
		}
		$result = strpos($haystack,$needle,$offset);
		if (is_int($result) AND $result > 0) return TRUE;
		elseif (is_int($result) AND $result === 0) return TRUE;
		elseif ($result === FALSE OR $result === '')return FALSE;
		elseif ($result === NULL) return FALSE;
		else return TRUE;
	}

	/**
	 * Create text indent with a given char
	 * @param integer $x
	 * @param string $chr
	 * @return string
	 */
	public function indent($x,$chr="\t") {
		$s = '';
		for ($i = 0; $i < $x; $i++) $s .= $chr;
		return $s;
	}
	
	/**
	 * Rotate by x 
	 * @param string $string
	 * @param integer $offset
	 * @return string
	 */
	public function rotx($string,$offset=0) {
		$length = strlen($string);
		$result = '';
		for($i = 0; $i < $length; $i++) {
			$ascii = ord($string{$i});
			$rotated = $ascii;
			if ($ascii > 64 AND $ascii < 91) {
				$rotated += $offset;
				$rotated > 90 AND $rotated += -90 + 64;
				$rotated < 65 AND $rotated += -64 + 90;
			}
			elseif ($ascii > 96 AND $ascii < 123) {
				$rotated += $offset;
				$rotated > 122 AND $rotated += -122 + 96;
				$rotated < 97 AND $rotated += -96 + 122;
			}
			$result .= chr($rotated);
		}
	return $result;
	}

	/**
	 * Hash a given string with MD5 Apr 1
	 * @param string $passwd
	 * @return string
	 */
	public function md5apr1($passwd) {
		$tmp = NULL;
		$salt = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'),0,8);
		$len = strlen($passwd);
		$text = $passwd.'$apr1$'.$salt;
		$bin = pack('H32', md5($passwd.$salt.$passwd));
		for($i = $len; $i > 0; $i -= 16) {
			$text .= substr($bin,0,min(16,$i));
		}
		for($i = $len; $i > 0; $i >>= 1) {
			$text .= ($i&1)?chr(0):$passwd{0};
		}
		$bin = pack('H32', md5($text));
		for($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $passwd : $bin;
			if ($i % 3) $new .= $salt;
			if ($i % 7) $new .= $passwd;
			$new .= ($i & 1) ? $bin : $passwd;
			$bin = pack('H32', md5($new));
		}
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) $j = 5;
			$tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
		}
		$tmp = chr(0).chr(0).$bin[11].$tmp;
		$tmp = strtr(
			strrev(substr(base64_encode($tmp), 2)),
			'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
			'./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
		);
		return '$apr1$'.$salt.'$'.$tmp;
	}
	
	/**
	 * Hash a given string with MD5 c211
	 * @param string $string
	 * @param string $prefix
	 * @param string $suffix
	 * @return string
	 */
	public function md5c211($string,$prefix=NULL,$suffix=NULL) {
		if (empty($string)) return $string;
		$hash = '';
		if (!empty($prefix)) $hash .= md5($prefix);
		$pass = str_split($string);
		foreach ($pass as $hashpass) $hash .= md5($hashpass);
		if (!empty($suffix)) $hash .= md5($suffix);
		$hash = md5($hash);
		return $hash;
	}

}

/**
 * LIB211 String Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211StringException extends LIB211BaseException {
}