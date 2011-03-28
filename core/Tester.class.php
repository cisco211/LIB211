<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
}

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
	 * Find a test by $name
	 * @param string $name
	 * @return boolean
	 */
	private function _findTest($name) {
		$this->tests = array();
		$this->_getTests();
		$count = count($this->tests);
		for ($i = 0; $i < $count; $i++) {
			if ($this->tests[$i]['name'] == $name) return $this->tests[$i]['file']; 
		}
		return FALSE;
	}
	
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
		if (!isset($this->results['LIB211TesterStatus']['time'])) $this->results['LIB211TesterStatus']['time'] = 0;
		$time_start = microtime(TRUE);
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
		$time_stop = microtime(TRUE);
		$time_diff = round($time_stop - $time_start,1);
		$this->results['LIB211TesterStatus']['time'] += $time_diff;
	}
	
	/**
	 * Set $tests to run
	 * @param array $tests
	 */
	private function _setTests($tests) {
		$override = array();
		foreach ($tests as $test) {
			$override[] = array('name'=>$test,'file'=>$this->_findTest($test));
		}
		$this->tests = array();
		$this->tests = $override;
	}
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Tester')) {
			$this->__check('c','LIB211TesterException');
			$this->__check('f','getenv');
			$this->__check('f','filemtime');
			$this->__check('f','microtime');
			$this->__check('f','round');
			$this->__check('v','_SERVER');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Tester',time());
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
	 * Get all test results
	 * @return multitype:
	 */
	public function getResults() {
		return $this->results;
	}
	
	/**
	 * Get all test results as HTML
	 * @return string
	 */
	public function getResultsAsHTML($name = NULL) {
		$status = $this->results['LIB211TesterStatus'];
		if ($name !== NULL) {
			$this->_setTests(array($name));
		}
		if (!empty($this->tests)) {
			$htmlTests = '';
			
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
				$color = '#CCFFCC';
				foreach ($result as $methodName => $methodResult) {
					if (isset($methodResult['status']) AND $methodResult['status'] == 'failed') {
						$check = 'FAILED';
						$color = '#FFCCCC';
						break;
					}
					foreach ($methodResult as $stepName => $stepResult) {
						if (isset($stepResult['status']) AND $stepResult['status'] == 'failed') {
							$check = 'FAILED';
							$color = '#FFCCCC';
						}
					}
				}
				if ($noTests) {
					$color = '#CCCCCC';
					$check = 'NOTEST';
				}
				$htmlTests .= '<table border="1" style="background-color:#CCCCCC;margin:0px 0px 20px 0px;width:100%;"><tr><td style="background-color:'.$color.';font-weight:bold;width:1px;">'.$check.'</td><td style="background-color:'.$color.';font-weight:bold;">'.$test['name'].'</td></tr>';
				
				//Methods
				foreach ($result as $methodName => $methodResult) {
					
					// Class
					if (isset($methodResult['status'])) {
						
						// Add class method failure message
						if ($methodResult['status'] == 'failed') {
							
							// Set method title
							$htmlTests .='<tr><td colspan="2"><table border="1" style="background-color:#AAAAAA;margin:5px;width:99%;"><tr><td style="background-color:#FFCCCC;font-weight:bold;width:1px;">FAILED</td><td style="background-color:#FFCCCC;font-weight:bold;">'.$methodName.'</td></tr>';
							
							// Set exception message
							if (isset($methodResult['exception'])) {
								$htmlTests .= '<tr><td colspan="2" style="background-color:#FFCCCC;font-family:monospace,\'courier new\';overflow:auto;white-space:pre-wrap;width:auto;">'.$methodResult['exception']->__toDefault().'</td></tr>';
								$htmlTests .= '<tr><td colspan="2" style="background-color:#FFCCCC;font-family:monospace,\'courier new\';overflow:auto;white-space:pre-wrap;width:auto;"><code>'.$methodResult['exception']->getTraceAsString().'</code></td></tr>';
							}
							$htmlTests .= '</table></td></tr>';
						}
					}
					
					// Method
					else {
						
						// Set method title
						$check = ' PASSED';
						$color2 = '#CCFFCC';
						foreach ($methodResult as $stepName => $stepResult) {
							if (isset($stepResult['status']) AND $stepResult['status'] == 'failed') {
								$color2 = '#FFCCCC';
								$check = ' FAILED';
							}
						}
						$htmlTests .='<tr><td colspan="2"><table border="1" style="background-color:#AAAAAA;margin:5px;width:99%;"><tr><td style="background-color:'.$color2.';font-weight:bold;width:1px;">'.$check.'</td><td style="background-color:'.$color2.';font-weight:bold;">'.$methodName.'</td></tr>';
						
						// Set test method failure message
						foreach ($methodResult as $stepName => $stepResult) {
							if (isset($stepResult['exception'])) {
								if ($stepName == 'test') $name = 'runTestMethod';
								else $name = $stepName;
								$htmlTests .= '<tr><td colspan="2"><table border="1" style="background-color:#888888;margin:5px;width:99%;"><tr><td style="background-color:#FFCCCC;font-weight:bold;width:1px;">FAILED</td><td style="background-color:#FFCCCC;font-weight:bold;">'.$name.'</td></tr>';
								$htmlTests .= '<tr><td colspan="2" style="background-color:#FFCCCC;font-family:monospace,\'courier new\';overflow:auto;white-space:pre-wrap;width:auto;">'.$stepResult['exception']->__toDefault().'</td></tr>';
								$htmlTests .= '<tr><td colspan="2" style="background-color:#FFCCCC;font-family:monospace,\'courier new\';overflow:auto;white-space:pre-wrap;width:auto;"><code>'.$stepResult['exception']->getTraceAsString().'</code></td></tr>';
								$htmlTests .= '</table></td></tr>';
							}
						}
						$htmlTests .= '</table></td></tr>';
					}
				}
				$htmlTests .= '</table>'; //testclass table
			}
		} else {
			return '<p>There are no tests</p>';
		}
		$htmlStatus = <<<ENDHTML
<table border="1" style="background-color:#CCCCCC;margin-bottom:20px;">
	<tr>
		<td colspan="2" style="font-weight:bold;text-align:center;">Status</td>
	</tr>
	<tr>
		<td align="right" style="background-color:#CCCCFF;">Runtime:</td>
		<td align="right" style="background-color:#CCCCFF;">{$status['time']}s</td>
	</tr>	
	<tr>
		<td align="right" style="background-color:#CCFFCC;">Passed:</td>
		<td align="right" style="background-color:#CCFFCC;">{$status['passed']}</td>
	</tr>
	<tr>
		<td align="right" style="background-color:#FFCCCC;">Failed:</td>
		<td align="right" style="background-color:#FFCCCC;">{$status['failed']}</td>
	</tr>
	<tr>
		<td align="right" style="background-color:#CCCCFF;">Total:</td>
		<td align="right" style="background-color:#CCCCFF;">{$status['tests']}</td>
	</tr>	
	</table>
ENDHTML;
		$htmlFinal = <<<ENDHTML
{$htmlStatus}
{$htmlTests}
{$htmlStatus}
ENDHTML;
		return $htmlFinal;
	}
	
	/**
	 * Get all test results as text
	 * @return string
	 */
	public function getResultsAsText($name = NULL) {
		$status = $this->results['LIB211TesterStatus'];
		
		if ($name !== NULL) {
			$this->_setTests(array($name));
		}
		
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
Runtime: {$status['time']}s
Passed: {$status['passed']}
Failed: {$status['failed']}
Total: {$status['tests']}


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
	
	/**
	 * Set $tests to run
	 * @param array $tests
	 */
	public function setTests($tests) {
		$this->_setTests($tests);
	}
	
}

/**
 * LIB211 Tester Exception
 * @author C!$C0^211
 *
 */
class LIB211TesterException extends LIB211BaseException {
}