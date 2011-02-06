<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

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
		
	/**
	 * Runs a test
	 * @param string $test
	 * @throws LIB211TesterException
	 */
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
		$result["tests"] = $this->tests;
		$result["results"] = $this->results;
		return $result;
	}
	
	/**
	 * Get a test result
	 * @param string $name
	 * @return array
	 */
	public function getResult($name) {
		if (isset($this->results[$name])) return $this->results[$name];
		else return array();
	}
	
	/**
	 * Get a test result as HTML
	 * @param string $name
	 */
	public function getResultAsHTML($name) {
	}
	
	/**
	 * Get a test result as text
	 * @param string $name
	 * @return string
	 */
	public function getResultAsText($name) {
		$status = $this->results['LIB211TesterStatus'];
		
		$tests = '';
		
		if (!empty($this->tests)) {
			
			foreach ($this->tests as $test) {
				
				if ($test['name'] == $name) {
				
					$result = $this->getResult($test['name']);
				
					// No Tests?
					$noTests = TRUE;
					foreach($result as $methodName => $methodResult) {
						if (preg_match('/^test[a-zA-Z\_]+[a-zA-Z0-9\_]*$/',$methodName)) $noTests = FALSE;
					}
					
					// Set class title
					$check = 'PASSED';
					foreach ($result as $methodName => $methodResult) {
						if (isset($methodResult['status']) AND $methodResult['status'] == 'failed') {
							$check = 'FAILED';
							break;
						}
						foreach ($methodResult as $stepName => $stepResult) {
							if (isset($stepResult['status']) AND $stepResult['status'] == 'failed') $check = 'FAILED';
						}
					}
					if ($noTests)  $check = 'NOTEST';
					$tests .= EOL.$check.' '.$test['name'];
					
					//Methods
					foreach ($result as $methodName => $methodResult) {
						
						// Class
						if (isset($methodResult['status'])) {
							
							// Add class method failure message
							if ($methodResult['status'] == 'failed') {
							
								// Set method title
								$tests .= EOL.' FAILED '.$methodName;
								
								// Set exception message
								if (isset($methodResult['exception'])) {
									$tests .= EOL.'  '.$methodResult['exception']->__toDefault();
									$tests .= EOL.'   '.str_replace(array(EOL,chr(13),chr(10)),EOL.'   ',$methodResult['exception']->getTraceAsString());
								}
							}
						}
						// Method
						else {
							
							// Set method title
							$check = ' PASSED';
							foreach ($methodResult as $stepName => $stepResult) {
								if (isset($stepResult['status']) AND $stepResult['status'] == 'failed') $check = ' FAILED';
							}
							$tests .= EOL.$check.' '.$methodName;
							
							// Set test method failure message
							foreach ($methodResult as $stepName => $stepResult) {
								if (isset($stepResult['exception'])) {
									if ($stepName == 'test') $name = 'runTestMethod';
									else $name = $stepName;
									$tests .= EOL.'  FAILED '.$name;
									$tests .= EOL.'   '.$stepResult['exception']->__toDefault();
									$tests .= EOL.'    '.str_replace(array(EOL,chr(13),chr(10)),EOL.'    ',$stepResult['exception']->getTraceAsString());
								}
							}
						}
					}
					
					$tests .= EOL;
					
				}
			}

		}
		else {
			$tests = EOL.'There are no tests';
		}
		
		$text = <<<ENDTEXT
{$tests}
Passed tests: {$status['passed']}
Failed tests: {$status['failed']}
Tests total: {$status['tests']}


ENDTEXT;
		return $text;
		
	}
	
	/**
	 * Get all test results
	 * @return multitype:
	 */
	public function getResults() {
		return $this->results;
	}
	
	/**
	 * Get all test results as HTML
	 */
	public function getResultsAsHTML() {
	}
	
	/**
	 * Get all test results as text
	 * @return string
	 */
	public function getResultsAsText() {
		$status = $this->results['LIB211TesterStatus'];
		
		if (!empty($this->tests)) {
			
			$tests = '';
			
			// Testclass
			foreach ($this->tests as $test) {
				
				$result = $this->getResult($test['name']);
				
				// No Tests?
				$noTests = TRUE;
				foreach($result as $methodName => $methodResult) {
					if (preg_match('/^test[a-zA-Z\_]+[a-zA-Z0-9\_]*$/',$methodName)) $noTests = FALSE;
				}
				
				// Set class title
				$check = 'PASSED';
				foreach ($result as $methodName => $methodResult) {
					if (isset($methodResult['status']) AND $methodResult['status'] == 'failed') {
						$check = 'FAILED';
						break;
					}
					foreach ($methodResult as $stepName => $stepResult) {
						if (isset($stepResult['status']) AND $stepResult['status'] == 'failed') $check = 'FAILED';
					}
				}
				if ($noTests)  $check = 'NOTEST';
				$tests .= EOL.$check.' '.$test['name'];
				
				//Methods
				foreach ($result as $methodName => $methodResult) {
					
					// Class
					if (isset($methodResult['status'])) {
						
						// Add class method failure message
						if ($methodResult['status'] == 'failed') {
							
							// Set method title
							$tests .= EOL.' FAILED '.$methodName;
							
							// Set exception message
							if (isset($methodResult['exception'])) {
								$tests .= EOL.'  '.$methodResult['exception']->__toDefault();
								$tests .= EOL.'   '.str_replace(array(EOL,chr(13),chr(10)),EOL.'   ',$methodResult['exception']->getTraceAsString());
							}
						}
					}
					// Method
					else {
						
						// Set method title
						$check = ' PASSED';
						foreach ($methodResult as $stepName => $stepResult) {
							if (isset($stepResult['status']) AND $stepResult['status'] == 'failed') $check = ' FAILED';
						}
						$tests .= EOL.$check.' '.$methodName;
						
						// Set test method failure message
						foreach ($methodResult as $stepName => $stepResult) {
							if (isset($stepResult['exception'])) {
								if ($stepName == 'test') $name = 'runTestMethod';
								else $name = $stepName;
								$tests .= EOL.'  FAILED '.$name;
								$tests .= EOL.'   '.$stepResult['exception']->__toDefault();
								$tests .= EOL.'    '.str_replace(array(EOL,chr(13),chr(10)),EOL.'    ',$stepResult['exception']->getTraceAsString());
							}
						}
					}
				}
				
				$tests .= EOL;
			}
		}
		else {
			$tests = EOL.'There are no tests';
		}
		
		$text = <<<ENDTEXT
{$tests}
Passed tests: {$status['passed']}
Failed tests: {$status['failed']}
Tests total: {$status['tests']}


ENDTEXT;
		return $text;
	}
	
	/**
	 * Get a list of all tests
	 * @return array
	 */
	public function getTests() {
		return $this->tests;
	}
	
	/**
	 * Run a test
	 * @param string $test
	 * @throws LIB211TesterException
	 */
	public function runTest($test) {
		foreach ($this->tests as $case) {
			if ($test == $case['name']) {
				$this->_runTest($case);
				return;
			}
		}
		throw new LIB211TesterException('Test "'.$test.'" not found');
	}
	
	/**
	 * Run all tests
	 */
	public function runTests() {
		foreach ($this->tests as $test) $this->_runTest($test);
	}
	
}

/**
 * LIB211 Tester Exception
 * @author C!$C0^211
 *
 */
class LIB211TesterException extends LIB211BaseException {}