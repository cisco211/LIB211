<?php
/**
 * @package LIB211
 */

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

/**
 * Include required files 
 */
if (LIB211_AUTOLOAD === FALSE) {
	require_once (LIB211_ROOT.'/module/Geohash/Geohash.class.php');
	require_once (LIB211_ROOT.'/module/String/String.class.php');
}

/**
 * LIB211 Random datatypes generator
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211Random extends LIB211Base {

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
	 * Integer maximum range
	 * @var integer
	 */
	private $rangeIntegerMax = NULL;
	
	/**
	 * Integer minimum range
	 * @var integer
	 */
	private $rangeIntegerMin = NULL;
		
	/**
	 * Loop maximum runs
	 * @var integer
	 */
	private $loopMaxRuns = NULL;
	
	/**
	 * List of strings
	 * @var array
	 */
	private $stringList = array();
	
	/**
	 * Fill string to a given size in any direction and any fill characters
	 * @param string $string
	 * @param integer $size
	 * @param string $dir
	 * @param string $chr
	 * @return string
	 */
	private function _strfill($string,$size,$dir='l',$chr='0') {
		$stringObj = new LIB211String();
		$result = $stringObj->fill($string,$size,$dir,$chr);
		unset($stringObj);
		return $result;
	}
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Random')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211RandomException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Random',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);
		
		// Detect highest integer
		if (!defined('PHP_INT_MAX')) {
			$max = 0x7FFF;
			$probe = 0x7FFFFFFF;
			while ($max == ($probe >> 16)) {
				$max = $probe;
				$probe = ($probe << 16) + 0xFFFF;
			}
			$IntegerMax = $max;
		}
		else $IntegerMax = PHP_INT_MAX;
		
		$this->setIntegerMax($IntegerMax);
		$this->setIntegerMin('-'.$IntegerMax);
		$this->setLoopMaxRuns(10);
		$this->setStringList(array('foo','bar','baz'));
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
	 * Get architecture size in bits
	 * @return integer
	 */
	public function getArchitecture() {
		if (defined('PHP_INT_SIZE')) return (PHP_INT_SIZE*8);
		else return 0;
	}

	/**
	 * Get loop maximum runs
	 * @return integer
	 */
	public function getLoopMaxRuns() {
		return $this->loopMaxRuns;
	}

	/**
	 * Get random boolean
	 * @param string $format
	 * @return array|boolean|number|NULL|StdClass|string
	 */
	public function getBoolean($format=NULL) {
		$value = (boolean)mt_rand(0,1);
		switch ($format) {
			case 'a': case 'ary': case 'array':
				return (array)$value;
			break;
			case 'b': case 'bool': case 'boolean':
				return (boolean)$value;
			break;
			case 'f': case 'flt': case 'float':
				return (float)$value;
			break;
			case 'i': case 'int': case 'integer':
				return (integer)$value;
			break;
			case 'n': case 'nul': case 'null':
				return (unset)$value;
			break;
			case 'o': case 'obj': case 'object':
				return (object)$value;
			break;
			case 's': case 'str': case 'string':
				if ($value === TRUE) return 'true';
				else return 'false';
			break;
			case 'S': case 'Str': case 'String':
				if ($value === TRUE) return 'TRUE';
				else return 'FALSE';
			break;
			case NULL: default:
				return $value;
			break;
		}
	}
		
	/**
	 * Get random float
	 * @param integer $min
	 * @param integer $max
	 * @param integer $precision
	 * @return float
	 */
	public function getFloat($min=NULL,$max=NULL,$precision=NULL) {
		$integer = $this->getInteger($min,$max);
		$fraction = '';
		$maxPrecision = 10;
		if ($precision === NULL OR $precision > $maxPrecision) $precision = $maxPrecision;
		for ($i = 0; $i < $precision; $i++) {
			if ($i == $precision-1) $fraction .= mt_rand(1,9);
			else $fraction .= mt_rand(0,9);
		}
		return (float)($integer.'.'.$fraction);
	}

	/**
	 * Get random negative float
	 * @param integer $min
	 * @param integer $max
	 * @param integer $precision
	 * @return float
	 */
	public function getFloatNegative($min=NULL,$max=NULL,$precision=NULL) {
		$integer = $this->getIntegerNegative($min,$max);
		$fraction = '';
		$maxPrecision = 10;
		if ($precision === NULL OR $precision > $maxPrecision) $precision = $maxPrecision;
		for ($i = 0; $i < $precision; $i++) {
			if ($i == $precision-1) $fraction .= mt_rand(1,9);
			else $fraction .= mt_rand(0,9);
		}
		return (float)($integer.'.'.$fraction);
	}
	
	/**
	 * Get random positive float
	 * @param integer $min
	 * @param integer $max
	 * @param integer $precision
	 * @return float
	 */
	public function getFloatPositive($min=NULL,$max=NULL,$precision=NULL) {
		// TODO: Check for reversed values
		$integer = $this->getIntegerPositive($min,$max);
		$fraction = '';
		$maxPrecision = 10;
		if ($precision === NULL OR $precision > $maxPrecision) $precision = $maxPrecision;
		for ($i = 0; $i < $precision; $i++) {
			if ($i == $precision-1) $fraction .= mt_rand(1,9);
			else $fraction .= mt_rand(0,9);
		}
		return (float)($integer.'.'.$fraction);
	}
	
	/**
	 * Get random geohash
	 * @return string
	 */
	public function getGeohash() {
		$geohashObj = new LIB211Geohash();
		while (TRUE) {
			$result = $geohashObj->encode($this->getLatitude(),$this->getLongitude());
			if (is_string($result)) break;
		}
		unset($geohashObj);
		return $result;
	}
	
	/**
	 * Get random integer
	 * @param integer $min
	 * @param integer $max
	 * @return integer
	 */
	public function getInteger($min=NULL,$max=NULL) {
		// TODO: Check for reversed values
		if ($min === NULL) $min = $this->getIntegerMin();
		if ($max === NULL) $max = $this->getIntegerMax();
		if ($min === $max) return $max;
		if ($min <= $this->getIntegerMin()) $min = $this->getIntegerMin();
		if ($max >= $this->getIntegerMax()) $max = $this->getIntegerMax();
		if (mt_rand(0,1) === 0) {
			$i = 0;
			while (TRUE) {
				$i++;
				$value = (integer)('-'.mt_rand(0,abs($min)));
				if ($value <= 0 AND $value >= $min) break;
				if ($i == $this->getLoopMaxRuns()) break;
			}
		}
		else {
			$i = 0;
			while (TRUE) {
				$i++;
				$value = (integer)mt_rand(0,$max);
				if ($value >= 0 AND $value <= $max) break;
				if ($i == $this->getLoopMaxRuns()) break;
			}
		}
		return $value;
	}
	
	/**
	 * Get random negative integer
	 * @param integer $min
	 * @param integer $max
	 * @return integer
	 */
	public function getIntegerNegative($min=NULL,$max=NULL) {
		// TODO: Check for reversed values
		if ($min === NULL) $min = $this->getIntegerMin();
		if ($max === NULL) $max = 0;
		if ($min === $max) return $max;
		if ($min <= $this->getIntegerMin()) $min = $this->getIntegerMin();
		if ($max >= 0) $max = 0;
		$i = 0;
		while (TRUE) {
			$i++;
			$value = (integer)('-'.mt_rand(abs($min),abs($max)));
			if ($value <= 0 AND $value >= $max) break;
			if ($i == $this->getLoopMaxRuns()) break;
		}
		return $value;
	}

	/**
	 * Get maximum integer range
	 * @return integer
	 */
	public function getIntegerMax() {
		return $this->rangeIntegerMax;
	}

	/**
	 * Get minimum integer range
	 * @return integer
	 */
	public function getIntegerMin() {
		return $this->rangeIntegerMin;
	}
	
	/**
	 * Get random positive integer
	 * @param integer $min
	 * @param integer $max
	 * @return integer
	 */
	public function getIntegerPositive($min=NULL,$max=NULL) {
		if ($min === NULL) $min = 0;
		if ($max === NULL) $max = $this->getIntegerMax();
		if ($min === $max) return $max;
		if ($min <= 0) $min = 0;
		if ($max >= $this->getIntegerMax()) $max = $this->getIntegerMax();
		$i = 0;
		while (TRUE) {
			$i++;
			$value = (integer)mt_rand($min,$max);
			if ($value >= 0 AND $value <= $max) break;
			if ($i == $this->getLoopMaxRuns()) break;
		}
		return $value;
	}
	
	/**
	 * Get random IPv4 address
	 * @param string $min
	 * @param string $max
	 * @param boolean $upperCase
	 * @return string
	 */
	public function getIPv4($min=NULL,$max=NULL) {
		$pattern = '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/';
		$defaultMin = '0.0.0.0';
		$defaultMax = '255.255.255.255';
		if ($this->getArchitecture() < 16) return $defaultMin;
		if (!is_string($min)) $min = $defaultMin;
		if (!(preg_match($pattern,$min) === 1)) $min = $defaultMin;
		$min = explode('.',$min);
		foreach ($min as $k => $v) {
			$min[$k] = (integer)base_convert($v,10,10);
			if ($min[$k] <= 0) $min[$k] = 0;
			if ($min[$k] >= 0xFFFF) $min[$k] = 0xFFFF;
		}
		if (!is_string($max)) $max = $defaultMax;
		if (!(preg_match($pattern,$max) === 1)) $max = $defaultMax;
		$max = explode('.',$max);
		foreach ($max as $k => $v) {
			$max[$k] = (integer)base_convert($v,10,10);
			if ($max[$k] <= 0) $max[$k] = 0; 
			if ($max[$k] >= 0xFFFF) $max[$k] = 0xFFFF;
		}
		$output = array();
		for ($i = 0; $i < 4; $i++) $output[$i] = (string)$this->getIntegerPositive($min[$i],$max[$i]);
		return implode('.',$output);
	}
	
	/**
	 * Get random IPv6 address
	 * @param string $min
	 * @param string $max
	 * @param boolean $upperCase
	 * @return string
	 */
	public function getIPv6($min=NULL,$max=NULL,$upperCase=FALSE) {
		$pattern = '/^[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}$/';
		$defaultMin = '0000:0000:0000:0000:0000:0000:0000:0000';
		$defaultMax = 'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff';
		if ($this->getArchitecture() < 32) return $defaultMin;
		if (!is_string($min)) $min = $defaultMin;
		if (!(preg_match($pattern,$min) === 1)) $min = $defaultMin;
		$min = explode(':',$min);
		foreach ($min as $k => $v) {
			$min[$k] = (integer)base_convert($v,16,10);
			if ($min[$k] <= 0) $min[$k] = 0;
			if ($min[$k] >= 0xFFFF) $min[$k] = 0xFFFF;
		}
		if (!is_string($max)) $max = $defaultMax;
		if (!(preg_match($pattern,$max) === 1)) $max = $defaultMax;
		$max = explode(':',$max);
		foreach ($max as $k => $v) {
			$max[$k] = (integer)base_convert($v,16,10);
			if ($max[$k] <= 0) $max[$k] = 0; 
			if ($max[$k] >= 0xFFFF) $max[$k] = 0xFFFF;
		}
		$output = array();
		for ($i = 0; $i < 8; $i++) $output[$i] = (string)$this->_strfill(base_convert($this->getIntegerPositive($min[$i],$max[$i]),10,16),4,'0');
		if ($upperCase === TRUE) return strtoupper(implode(':',$output));
		else return implode(':',$output);
	}
	
	/**
	 * Get random Latitude
	 * @param integer $min
	 * @param integer $max
	 * @param integer $precision
	 * @return float
	 */
	public function getLatitude($min=NULL,$max=NULL,$precision=NULL) {
		if ($min === NULL) $min = -90;
		if ($max === NULL) $max = 90;
		if ($min <= -90.0) $min = -90;
		if ($max >= 90.0) $max = 90;
		return $this->getFloat($min,$max,$precision);
	}

	/**
	 * Get random Longitude
	 * @param integer $min
	 * @param integer $max
	 * @param integer $precision
	 * @return float
	 */
	public function getLongitude($min=NULL,$max=NULL,$precision=NULL) {
		if ($min === NULL) $min = -180;
		if ($max === NULL) $max = 180;
		if ($min <= -180.0) $min = -180;
		if ($max >= 180.0) $max = 180;
		return $this->getFloat($min,$max,$precision);
	}
	
	/**
	 * Get random MD5
	 * @return string
	 */
	public function getMd5() {
		$charlist = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
		return md5($this->getStringSequence(128,$charlist));
	}
	
	/**
	 * Get random null
	 * @param string $format
	 * @return array|boolean|number|NULL|StdClass|string
	 */
	public function getNull($format=NULL) {
		switch ($format) {
			case 'a': case 'ary': case 'array':
				return (array)NULL;
				break;
			case 'b': case 'bool': case 'boolean':
				return (boolean)NULL;
				break;
			case 'f': case 'flt': case 'float':
				return (float)NULL;
				break;
			case 'i': case 'int': case 'integer':
				return (integer)NULL;
				break;
			case 'n': case 'nul': case 'null':
				return (unset)NULL;
				break;
			case 'o': case 'obj': case 'object':
				return (object)NULL;
				break;
			case 's': case 'str': case 'string':
				return (string)NULL;
				break;
			case 't': case 'txt': case 'text':
				return 'null';
				break;
			case 'T': case 'Txt': case 'Text':
				return 'NULL';
				break;
			case 'u': case 'utx': case 'utext':
				return 'nul';
				break;
			case 'U': case 'Utx': case 'Utext':
				return 'NUL';
				break;
			case NULL: default:
				return NULL;
				break;
		}
	}
	
	/**
	 * Get random timestamp
	 * @param integer $min
	 * @param integer $max
	 * @return integer
	 */
	public function getTimestamp($min=NULL,$max=NULL) {
		if ($min === NULL) $min = 0;
		if ($min < 0) $min = 0;
		if ($max === NULL) $max = 2147483647;
		if ($max > 2147483647) $max = 2147483647;
		return $this->getIntegerPositive($min,$max);
	}

	public function getString($options = array(),$wordList = NULL) {
		$rnd = create_function('&$obj','return $obj->getStringList(mt_rand(0,count($obj->getStringList())-1));');
		if (is_array($wordList)) {
			$oldList = $this->getStringList();
			$this->setStringList(NULL);
			$this->setStringList($wordList);
		}
		if (is_array($options) AND !empty($options)) {
			if (!isset($options['wordcount'])) $options['wordcount'] = 1;
			else $options['wordcount'] = (integer)$options['wordcount'];
			if (!isset($options['prefix'])) $options['prefix'] = '';
			else $options['prefix'] = (string)$options['prefix'];
			if (!isset($options['startprefix'])) $options['startprefix'] = FALSE;
			else $options['startprefix'] = (boolean)$options['startprefix'];
			if (!isset($options['suffix'])) $options['suffix'] = '';
			else $options['suffix'] = (string)$options['suffix'];
			if (!isset($options['endsuffix'])) $options['endsuffix'] = FALSE;
			else $options['endsuffix'] = (boolean)$options['endsuffix'];
			if (!isset($options['innerprefix'])) $options['innerprefix'] = '';
			else $options['innerprefix'] = (string)$options['innerprefix'];
			if (!isset($options['innersuffix'])) $options['innersuffix'] = '';
			else $options['innersuffix'] = (string)$options['innersuffix'];
			$string = '';
			for ($i = 0; $i < $options['wordcount']; $i++) {
				if ($i == 0 AND $options['startprefix'] === TRUE) $string .= $options['prefix'];
				elseif ($i != 0) $string .= $options['prefix'];
				$string .= $options['innerprefix'].$rnd($this).$options['innersuffix'];
				if ($i == ($options['wordcount']-1) AND $options['endsuffix'] === TRUE) $string .= $options['suffix'];
				elseif ($i != ($options['wordcount']-1)) $string .= $options['suffix'];
			}
		}
		else $string = $rnd($this);
		if (isset($oldList) AND is_array($oldList)) {
			$this->setStringList(NULL);
			$this->setStringList($oldList);
		}
		return $string;
	}
	
	/**
	 * Return string list or an entry for a given index
	 * @param integer $index
	 * @return array|string
	 */
	public function getStringList($index = NULL) {
		if (is_integer($index) AND isset($this->stringList[$index])) return $this->stringList[$index];
		else return $this->stringList;
	}
	
	/**
	 * Generates a random string by given size and charlist
	 * @param integer $length
	 * @param characters $chars
	 * @return string
	 */
	public function getStringSequence($length,$chars = NULL) {
		if ($chars === NULL) $chars='abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = '';
		$chars_length = strlen($chars);
		for($i=0; $i < $length; $i++) $string .= $chars[mt_rand(0,$chars_length-1)];
		return (string)$string;
	}
	
	/**
	 * Set max loop runs
	 * @param integer $loops
	 * @return boolean
	 */
	public function setLoopMaxRuns($loops) {
		$this->loopMaxRuns = (integer)$loops;
		return TRUE;
	}

	/**
	 * Set integer range to 16 bit
	 * @return boolean
	 */
	public function setInteger16Bit() {
		if ($this->getArchitecture() >= 16) {
			$this->setIntegerMax((integer)(+32767));
			$this->setIntegerMin((integer)(-32767));
			return TRUE;
		}
		else return FALSE;
	}

	/**
	 * Set integer range to 32 bit
	 * @return boolean
	 */
	public function setInteger32Bit() {
		if ($this->getArchitecture() >= 32) {
			$this->setIntegerMax((integer)(+2147483647));
			$this->setIntegerMin((integer)(-2147483647));
			return TRUE;
		}
		else return FALSE;
	}
	
	/**
	 * Set integer range to 64 bit
	 * @return boolean
	 */
	public function setInteger64Bit() {
		if ($this->getArchitecture() >= 64) {
			$this->setIntegerMax((integer)(+9223372036854775807));
			$this->setIntegerMin((integer)(-9223372036854775807));
			return TRUE;
		}
		else return FALSE;
	}

	/**
	 * Set integer range to 8 bit
	 * @return boolean
	 */
	public function setInteger8Bit() {
		if ($this->getArchitecture() >= 8) {
			$this->setIntegerMax((integer)(+127));
			$this->setIntegerMin((integer)(-127));
			return TRUE;
		}
		else return FALSE;
	}
	
	/**
	 * Set maximum integer range
	 * @param integer $value
	 * @return boolean
	 */
	public function setIntegerMax($value) {
		$this->rangeIntegerMax = (integer)$value;
		return TRUE;
	}

	/**
	 * Set minimum integer range
	 * @param integer $value
	 * @return boolean
	 */
	public function setIntegerMin($value) {
		$this->rangeIntegerMin = (integer)$value;
		return TRUE;
	}
	
	/**
	 * Add string(s) to string list
	 * @param mixed $data
	 * @return boolean
	 */
	public function setStringList($data=FALSE) {
		switch (gettype($data)) {
			case 'array':
				foreach ($data as $string) $this->stringList[] = $string;
			break;
			case 'NULL':
				$this->stringList = array();
			break;
			case 'string':
				$this->stringList[] = $data;
			break;
			default:
			break;
		}
		//shuffle($this->stringList);
		
		return TRUE;
	}
	
}

/**
 * LIB211 Random Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211RandomException extends LIB211BaseException {
}