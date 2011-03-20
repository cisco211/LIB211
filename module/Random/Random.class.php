<?php

/**
 * LIB211 Random datatypes generator
 * 
 * @author C!$C0^211
 *
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
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $rangeIntegerMin = NULL;
	
	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $rangeIntegerMax = NULL;
	
	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $rangeTimestampMax = NULL;
	
	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $loopMaxRuns = NULL;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/lib211.lock')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211RandomException');
			touch(LIB211_ROOT.'/lib211.lock',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);

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
		$this->setRangeIntegerMax($IntegerMax);
		$this->setRangeIntegerMin('-'.$IntegerMax);
		$this->setRangeTimestampMax(2147483647);
		$this->setLoopMaxRuns(10);
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
	 * Enter description here ...
	 * @return number
	 */
	public function getArchitecture() {
		if (defined('PHP_INT_SIZE')) return (PHP_INT_SIZE*8);
		else return 0;
	}

	/**
	 * Enter description here ...
	 * @return unknown_type
	 */
	public function getLoopMaxRuns() {
		return $this->loopMaxRuns;
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $format
	 * @return array|boolean|number|NULL|StdClass|string
	 */
	public function getRandomBoolean($format=NULL) {
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

	public function getRandomFloat($min=NULL,$max=NULL) {
	}

	public function getRandomFloatNegative($min=NULL,$max=NULL) {
	}
	
	public function getRandomFloatPositive($min=NULL,$max=NULL) {
	}
	
	public function getRandomGeohash() {
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $min
	 * @param unknown_type $max
	 * @return number
	 */
	public function getRandomInteger($min=NULL,$max=NULL) {
		if ($min === NULL) $min = $this->getRangeIntegerMin();
		if ($max === NULL) $max = $this->getRangeIntegerMax();
		if ($min <= $this->getRangeIntegerMin()) $min = $this->getRangeIntegerMin();
		if ($max >= $this->getRangeIntegerMax()) $max = $this->getRangeIntegerMax();
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
	 * Enter description here ...
	 * @param unknown_type $min
	 * @param unknown_type $max
	 * @return number
	 */
	public function getRandomIntegerNegative($min=NULL,$max=NULL) {
		if ($min === NULL) $min = $this->getRangeIntegerMin();
		if ($max === NULL) $max = 0;
		if ($min <= $this->getRangeIntegerMin()) $min = $this->getRangeIntegerMin();
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
	 * Enter description here ...
	 * @param unknown_type $min
	 * @param unknown_type $max
	 * @return number
	 */
	public function getRandomIntegerPositive($min=NULL,$max=NULL) {
		if ($min === NULL) $min = 0;
		if ($max === NULL) $max = $this->getRangeIntegerMax();
		if ($min <= 0) $min = 0;
		if ($max >= $this->getRangeIntegerMax()) $max = $this->getRangeIntegerMax();
		$i = 0;
		while (TRUE) {
			$i++;
			$value = (integer)mt_rand($min,$max);
			if ($value >= 0 AND $value <= $max) break;
			if ($i == $this->getLoopMaxRuns()) break;
		}
		return $value;
	}
	
	public function getRandomLatitude($min=NULL,$max=NULL) {
		if ($min === NULL) $min = -90.0;
		if ($max === NULL) $max = 90.0;
		if ($min <= -90.0) $min = -90.0;
		if ($max >= 90.0) $max = 90.0;
		
	}

	public function getRandomLongitude($min=NULL,$max=NULL) {
		if ($min === NULL) $min = -180.0;
		if ($max === NULL) $max = 180.0;
		if ($min <= -180.0) $min = -180.0;
		if ($max >= 180.0) $max = 180.0;
		
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $format
	 * @return array|boolean|number|NULL|StdClass|string
	 */
	public function getRandomNull($format=NULL) {
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
	
	public function getRandomString() {}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $min
	 * @param unknown_type $max
	 * @return number
	 */
	public function getRandomTimestamp($min=NULL,$max=NULL) {
		if ($min === NULL) $min = 0;
		if ($min < 0) $min = 0;
		if ($max === NULL) $max = $this->getRangeTimestampMax();
		if ($max > $this->getRangeTimestampMax()) $max = $this->getRangeTimestampMax();
		return $this->getRandomIntegerPositive($min,$max);
	}

	/**
	 * Enter description here ...
	 * @return unknown_type
	 */
	public function getRangeIntegerMax() {
		return $this->rangeIntegerMax;
	}

	/**
	 * Enter description here ...
	 * @return unknown_type
	 */
	public function getRangeIntegerMin() {
		return $this->rangeIntegerMin;
	}

	/**
	 * Enter description here ...
	 * @return unknown_type
	 */
	public function getRangeTimestampMax() {
		return $this->rangeTimestampMax;
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $loops
	 * @return boolean
	 */
	public function setLoopMaxRuns($loops) {
		$this->loopMaxRuns = (integer)$loops;
		return TRUE;
	}

	/**
	 * Enter description here ...
	 * @return boolean
	 */
	public function setRangeInteger16Bit() {
		if ($this->getArchitecture() >= 16) {
			$this->setRangeIntegerMax((integer)(+32767));
			$this->setRangeIntegerMin((integer)(-32767));
			return TRUE;
		}
		else return FALSE;
	}

	/**
	 * Enter description here ...
	 * @return boolean
	 */
	public function setRangeInteger32Bit() {
		if ($this->getArchitecture() >= 32) {
			$this->setRangeIntegerMax((integer)(+2147483647));
			$this->setRangeIntegerMin((integer)(-2147483647));
			return TRUE;
		}
		else return FALSE;
	}
	
	/**
	 * Enter description here ...
	 * @return boolean
	 */
	public function setRangeInteger64Bit() {
		if ($this->getArchitecture() >= 64) {
			$this->setRangeIntegerMax((integer)(+9223372036854775807));
			$this->setRangeIntegerMin((integer)(-9223372036854775807));
			return TRUE;
		}
		else return FALSE;
	}

	/**
	 * Enter description here ...
	 * @return boolean
	 */
	public function setRangeInteger8Bit() {
		if ($this->getArchitecture() >= 8) {
			$this->setRangeIntegerMax((integer)(+127));
			$this->setRangeIntegerMin((integer)(-127));
			return TRUE;
		}
		else return FALSE;
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $value
	 * @return boolean
	 */
	public function setRangeIntegerMax($value) {
		$this->rangeIntegerMax = (integer)$value;
		return TRUE;
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $value
	 * @return boolean
	 */
	public function setRangeIntegerMin($value) {
		$this->rangeIntegerMin = (integer)$value;
		return TRUE;
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $value
	 * @return boolean
	 */
	public function setRangeTimestampMax($value) {
		$this->rangeTimestampMax = (integer)$value;
		return TRUE;
	}
	
}

/**
 * Enter description here ...
 * @author ts
 *
 */
class LIB211RandomException extends LIB211BaseException {
}