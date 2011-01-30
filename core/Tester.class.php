<?php

/**
 * LIB211 Testrunner
 * 
 * @author C!$C0^211
 *
 */
class LIB211Tester extends LIB211Base {

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
	 * Current test object
	 * @var object
	 */
	private $testObject = NULL;
	
	/**
	 * List of available tests (classname and path)
	 * @var array
	 */
	private $tests = array();
	
	/**
	 * Stores the results
	 * @var array
	 */
	private $results = array();
	
	/**
	 * Return list of module tests
	 * @return array
	 */
	private function _getModuleTests() {
		$path = LIB211_ROOT.'/module';
		$modules = scandir($path);
		$ignore = array('.','..');
		$list = array();
		foreach ($modules as $module) {
			if (!in_array($module,$ignore) AND is_dir($path.'/'.$module)) {
				if (file_exists($path.'/'.$module.'/'.$module.'.test.php')) {
					$list[] = array('name'=>'LIB211'.$module.'Test','file'=>$path.'/'.$module.'/'.$module.'.test.php');
				}
			}
		}
		return $list;
	}
	
	/**
	 * Get all tests into internal list
	 */
	private function _getTests() {
		$methods = get_class_methods('LIB211Tester');
		foreach ($methods as $method) {
			if (preg_match('/^\_get[a-zA-Z]+Tests$/',$method)) {
				$list = $this->$method();
				foreach ($list as $entry) {
					array_push($this->tests,$entry);
				}
			}
		}
	}
		
	private function _runTest($test) {
		if (empty($test)) throw new LIB211TesterException('Given test is empty');
		if (empty($test['name'])) throw new LIB211TesterException('No name for test specified');
		if (empty($test['file'])) throw new LIB211TesterException('No file for test "'.$test['name'].'" specified');
		if (!file_exists($test['file'])) throw new LIB211TesterException('Test file for "'.$test['name'].'" not found at "'.$test['file'].'"');
		try {
			include_once($test['file']);
		}
		catch (Exception $e) {
			throw new LIB211TesterException('Could not include test file "'.$test['file'].'" for test "'.$test['name'].'"');
		}
		$this->testObject = new $test['name'];
		if (!is_object($this->testObject)) throw new LIB211TesterException('No object given');
		$this->results[$test['name']]['setPrefixAll']['status'] = 'passed';
		try {
			$this->testObject->setPrefixAll();
		}
		catch (LIB211TestclassException $e) {
			$this->results[$test['name']]['setPrefixAll']['status'] = 'failed';
			$this->results[$test['name']]['setPrefixAll']['exception'] = $e;
		}
		$methods = get_class_methods(get_class($this->testObject));
		foreach ($methods as $method) {
			if (preg_match('/^test[a-zA-Z\_]+[a-zA-Z0-9\_]*$/',$method)) {
				$this->results[$test['name']][$method]['test']['status'] = 'passed';
				$passed = TRUE;
				$this->results['LIB211TesterStatus']['tests']++;
				$this->results[$test['name']][$method]['setPrefix']['status'] = 'passed';
				try {
					$this->testObject->setPrefix();
				}
				catch (LIB211TestclassException $e) {
					$passed = FALSE;
					$this->results[$test['name']][$method]['setPrefix']['status'] = 'failed';
					$this->results[$test['name']][$method]['setPrefix']['exception'] = $e;
				}
				try {
					$this->testObject->$method();
				}
				catch (LIB211TestclassException $e) {
					$passed = FALSE;
					$this->results[$test['name']][$method]['test']['status'] = 'failed';
					$this->results[$test['name']][$method]['test']['exception'] = $e;
				}
				$this->results[$test['name']][$method]['setSuffix']['status'] = 'passed';
				try {
					$this->testObject->setSuffix();
				}
				catch (LIB211TestclassException $e) {
					$passed = FALSE;
					$this->results[$test['name']][$method]['setSuffix']['status'] = 'failed';
					$this->results[$test['name']][$method]['setSuffix']['exception'] = $e;
					}
				if ($passed) $this->results['LIB211TesterStatus']['passed']++;
				else $this->results['LIB211TesterStatus']['failed']++;
			}
		}
		$this->results[$test['name']]['setSuffixAll']['status'] = 'passed';
		try {
			$this->testObject->setSuffixAll();
		}
		catch (LIB211TestclassException $e) {
			$this->results[$test['name']]['setSuffixAll']['status'] = 'failed';
			$this->results[$test['name']]['setSuffixAll']['exception'] = $e;
		}
	}
	
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
		require_once(LIB211_ROOT.'/core/Testclass.abstract.php');
		$this->_getTests();
		$this->results['LIB211TesterStatus']['tests'] = 0;
		$this->results['LIB211TesterStatus']['failed'] = 0;
		$this->results['LIB211TesterStatus']['passed'] = 0;
	}
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		parent::__destruct(); 
		self::$instances--;
	}
	
	public function __status() {
		self::$time_stop = microtime(TRUE);
		self::$time_diff = round(self::$time_stop - self::$time_start,11);
		$result = array();
		$result["instance"] = self::$instances;
		$result["runtime"] = self::$time_diff;
		$result["tests"] = $this->tests;
		$result["results"] = $this->results;
		return $result;
	}
	
	public function getResults() {
		return $this->results;
	}
	
	public function runTest($test) {
		foreach ($this->tests as $case) {
			if ($test == $case['name']) {
				$this->_runTest($case);
				return;
			}
		}
		throw new LIB211TesterException('Test "'.$test.' not found');
	}
	
	public function runTests() {
		foreach ($this->tests as $test) $this->_runTest($test);
	}
	
}

class LIB211TesterException extends LIB211BaseException {}