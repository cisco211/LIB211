<?php

/**
 * LIB211 Permutate 
 * Transform any count of lists (dimensions) to a new list where all entries are combined with each other (rainbow table)
 * 
 * @author C!$C0^211
 *
 */
class LIB211Permutate extends LIB211Base {

	/**
	 * Instance counter
	 * @var integer
	 */
	private static $instances = 0;
	
	/**
	 * Runtime of object
	 * @var float
	 */
	private static $time_diff = 0;
	
	/**
	 * Start time of object
	 * @var float
	 */
	private static $time_start = 0;
	
	/**
	 * Stop time of object
	 * @var float
	 */
	private static $time_stop = 0;
	
	/**
	 * Stores the lists
	 * @var array
	 */
	private $lists = array();

	/**
	 * Stores the result
	 * @var array
	 */
	private $result = array();
	
	/**
	 * Permutate the given lists and store result (Thanks to "combie" on phpforum.de)
	 * @return boolean
	 */
	private function _compute() {
		$status = TRUE;
		try {
			$this->result = array();
			foreach ($this->lists as $subarray) {
				$cache = array();
				if (empty($this->result)) {
					foreach ($subarray as $element) {
						$cache[] = array($element);
					}
				}
				else {
					foreach ($this->result as $old) {
						foreach ($subarray as $element) {
							$cache[] = array_merge($old,array($element));
						}
					}
				}
				$this->result = $cache;
			}
		}
		catch (LIB211PermutateException $e) {
			$status = FALSE;
		}
		return $status;
	}
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/lib211.lock')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211PermutateException');
			$this->__check('f','is_array');
			touch(LIB211_ROOT.'/lib211.lock',time());
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
	 * Add a list
	 * @param array $list
	 * @return boolean
	 */
	public function addList(array $list) {
		$status = TRUE;
		try {
			$this->lists[] = $list;
		}
		catch (LIB211PermutateException $e) {
			$status = FALSE;
		}
		return $status;
	}

	
	/**
	 * Add given lists
	 * @param array $arg0
	 * @param array $arg1
	 * @param array $arg2
	 * @param array ...
	 */
	public function addLists(array $arg0) {
		$status = TRUE;
		try {
			$argc = func_num_args();
			for ($i = 0; $i < $argc; $i++) {
				$arg = func_get_arg($i);
				if (is_array($arg)) $this->lists[] = $arg;
			}
		}
		catch (LIB211PermutateException $e) {
			$status = FALSE;
		}
		return $status;
	}
	
	/**
	 * Delete a list by a given id
	 * @param integer $id
	 * @return boolean
	 */
	public function delList(integer $id) {
		$status = TRUE;
		try {
			unset($this->lists[$id]);
		}
		catch (LIB211PermutateException $e) {
			$status = FALSE;
		}
		return $status;
	}
	
	/**
	 * Delete all lists
	 * @return boolean
	 */
	public function delLists() {
		$status = TRUE;
		try {
			$this->lists = array();
		}
		catch (LIB211PermutateException $e) {
			$status = FALSE;
		}
		return $status;
	}
	
	/**
	 * Get a list by a given id
	 * @param integer $id
	 * @return array
	 */
	public function getList(integer $id) {
		if (isset($this->lists[$id])) return $this->lists[$id];
		else return array();		
	}

	/**
	 *Get all lists
	 * @return array
	 */
	public function getLists() {
		return $this->lists;
	}
	
	/**
	 * Permutate the given lists and return result (Thanks to "combie" on phpforum.de)
	 * In csv/tsv format you can specify a second argument for separator and a third for quote character.
	 * @param string $format
	 * @return array
	 * 
	 */
	public function getResult($format = NULL) {
		$status = TRUE;
		try {
			$this->_compute();
		}
		catch (LIB211PermutateException $e) {
			$status = FALSE;
		}
		if ($status) {
			switch ($format) {
				case "csv": case "tsv";
					$separator = @func_get_arg(1);
					if (empty($separator)) $separator = ',';
					$quote = @func_get_arg(2);
					if (empty($quote)) $quote = '';
					$output = "";
					foreach ($this->result as $line) {
						$count = count($line);
						for ($i = 0; $i < $count; $i++) {
							$output .= $quote.$line[$i].$quote;
							if (($count-1) != $i) {
								$output .= $separator;
							}
						}
						$output .= EOL;
					}
					return $output;
				break;
				default:
					return $this->result;
				break;
			}	
		}
		return array();
	}

}

/**
 * LIB211 Permutate Exception
 * @author C!$C0^211
 *
 */
class LIB211PermutateException extends LIB211BaseException {
}