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
 * LIB211 Counter
 *
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211Counter extends LIB211Base {

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

	public $value = 0;

	/**
	 * Constructor
	 */
	public function __construct($data=0) {
		parent::__construct();
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Counter')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211CounterException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Counter',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);
		$this->value = $this->setValue($data);
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
	 * Convert data array to a type
	 * @param mixed $data
	 * @param string $format
	 * @return mixed
	 */
	public function data2Type($data,$format='string') {
		if (!$this->validate($data)) return NULL;
		if (empty($data['LOW'])) $string = implode('',array_reverse($data['HIGH']));
		else $string = implode('',array_reverse($data['HIGH'])).'.'.implode('',$data['LOW']);
		if ($data['NEGATIVE'] === TRUE) $string = '-'.$string;
		switch ($format) {
			case 'f': case 'float': return (float)$string; break;
			case 'i': case 'integer':	return (integer)$string; break;
			case 's': case 'string': default: return (string)$string; break;
		}
	}

	public function getValue($format='string') {
		switch ($format) {
			case 'a': case 'array': case 'd': case 'data': case 'ray': return $this->value; break;
			default: return $this->data2Type($this->value,$format); break;
		}
	}

	#NEGATIVES?
	public function mathAdd($input,$input2=NULL) {
		if ($this->validate($input)) $data = $input;
		else $data = $this->type2Data($input);
		if($input2 !== NULL) {
			if ($this->validate($input2)) $result = $input2;
			else $result = $this->type2Data($input2);
		} else $result = $this->value;
		if (!empty($data['LOW'])) {
			$lowLength = count($data['LOW']);
			for ($i = $lowLength-1; $i >= 0; $i--) {
				if (!isset($result['LOW'][$i])) $result['LOW'][$i] = 0;
				$test = $result['LOW'][$i]+$data['LOW'][$i];
				if ($test >= 10) {
					if ($i == 0) $result['HIGH'][0] += 1;
					else $result['LOW'][$i-1] += 1;
					$result['LOW'][$i] = $test-10;
				} else {
					$result['LOW'][$i] += $data['LOW'][$i];
				}
			}
		}
		$highLength = count($data['HIGH']);
		for ($i = 0; $i < $highLength; $i++) {
			if (!isset($result['HIGH'][$i])) $result['HIGH'][$i] = 0;
			$test = $result['HIGH'][$i]+$data['HIGH'][$i];
			if ($test>=10) {
				if (!isset($result['HIGH'][$i+1])) $result['HIGH'][$i+1] = 0;
				$result['HIGH'][$i+1] += 1;
				$result['HIGH'][$i] = $test-10;
			} else {
				$result['HIGH'][$i] += $data['HIGH'][$i];
			}
		}
		if ($input2 === NULL) $this->value = $result;
		return $result;
	}

	public function setValue($data) {
		if ($this->validate($data)) $this->value = $data;
		else $this->value = $this->type2Data($data);
	}

	/**
	 * Convert type to an data array
	 * @param mixed $data
	 * @return array
	 */
	public function type2Data($data) {
		if ($this->validate($data)) return $data;
		$output = array('NEGATIVE'=>FALSE,'LOW'=>array(),'HIGH'=>array());
		$string = (string)$data;
		if(preg_match('/^\-/',$string)===1) {
			$output['NEGATIVE'] = TRUE;
			$string = str_replace('-','',$string);
		}
		if(preg_match('/\./',$string)===1) {
			$highLow = explode('.',$string);
			$low = $highLow[1];
			$high = $highLow[0];
		} else {
			$low = '';
			$high = $string;
		}
		$lowLength = strlen($low);
		for ($i = 0; $i < $lowLength; $i++) {
			$output['LOW'][$i] = (integer)substr($low,$i,1);
		}
		$highLength = strlen($high);
		for ($i = 0; $i < $highLength; $i++) {
			$output['HIGH'][$i] = (integer)substr($high,$i,1);
		}
		$output['HIGH'] = array_reverse($output['HIGH']);
		return $output;
	}

	public function validate($data) {
		if (!is_array($data)) return FALSE;
		if (!isset($data['NEGATIVE'])) return FALSE;
		if (!is_bool($data['NEGATIVE'])) return FALSE;
		if (!isset($data['LOW'])) return FALSE;
		if (!empty($data['LOW'])) {
			foreach ($data['LOW'] as $block) {
				if (!is_integer($block)) return FALSE;
				if ($block >= 10) return FALSE;
			}
		}
		if (!isset($data['HIGH'])) return FALSE;
		foreach ($data['HIGH'] as $block) {
			if (!is_integer($block)) return FALSE;
			if ($block >= 10) return FALSE;
		}
		return TRUE;
	}

}

/**
 * LIB211 Example Exception
 *
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211CounterException extends LIB211BaseException {
}