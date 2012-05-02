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
 * LIB211 Testclass abstraction
 * 
 * @author C!$C0^211
 * @package LIB211
 */
abstract class LIB211Testclass {
		
	public function __construct() {
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Testclass')) {
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Testclass',time());
		}
		assert_options(ASSERT_ACTIVE,1);
		assert_options(ASSERT_WARNING,0);
		assert_options(ASSERT_QUIET_EVAL,1);
		assert_options(ASSERT_CALLBACK,array(&$this,'assertionCallback'));		
	}
	
	/**
	 * Format a given variable to be "readable" in exception messages
	 * @param mixed $var
	 * @return string|number|mixed
	 */
	private function _formatType($var) {
		switch (gettype($var)) {
			case 'boolean':
				if ($var === TRUE) return (string)'boolean(TRUE)';
				elseif ($var === FALSE) return (string)'boolean(FALSE)';
				else return (string)'A boolean which is not a boolean... FAIL!';
			break;
			case 'integer':
				return 'integer('.(integer)$var.')';
			break;
			case 'double': case 'float':
				return 'float('.(float)$var.')';
			break;
			case 'string':
				return 'string('.(string)$var.')';
			break;
			case 'array':
				return 'array('.(string)print_r($var,TRUE).')';
			break;
			case 'object':
				return 'object('.(string)print_r($var,TRUE).')';
			break;
			case 'resource':
				return 'resource('.(string)$var.')';
			break;
			case 'NULL':
				return 'null(NULL)';
			break;
			case 'unknown type':
				return 'unknown('.(string)$var.')';
			break;
			default:
				return 'unknown('.(string)$var.')';
			break;
		}
	}
	
	/**
	 * Callback function for PHP's internal assertion mechanism
	 * @param string $file
	 * @param integer $line
	 * @throws LIB211TestclassException
	 */
	final public function assertionCallback($file,$line) {
		throw new LIB211TestclassException('Failed assertion at '.$file.':'.$line.' and thrown');
	}
	
	/**
	 * Assert that $left will be equals to $right
	 * @param mixed $left
	 * @param mixed $right
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	final public function assertEquals($left,$right,$checkType=TRUE) {
		$message = 'assertEquals failed: "'.$this->_formatType($left).'" not equals "'.$this->_formatType($right).'"';
		if ($checkType) {
			if ($left !== $right) {
				throw new LIB211TestclassException($message);
			}
		}
		else {
			if ($left != $right) { 
				throw new LIB211TestclassException($message);
			}
		}
	}

	/**
	 * Assert that $test will be false
	 * @param mixed $test
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	final public function assertFalse($test,$checkType=TRUE) {
		$message = 'assertFalse failed: "'.$this->_formatType($test).'" is not "FALSE"';
		if ($checkType) {
			if ($test !== FALSE) {
				throw new LIB211TestclassException($message);
			}
		}
		else {
			if ($test != FALSE) { 
				throw new LIB211TestclassException($message);
			}
		}
	}
	
	/**
	 * Assert that $left will be greater $right
	 * @param mixed $left
	 * @param mixed $right
	 * @throws LIB211TestclassException
	 */
	final public function assertGreater($left,$right) {
		$message = 'assertGreater failed: "'.$this->_formatType($left).'" not greater "'.$this->_formatType($right).'"';
		if (!($left > $right)) { 
			throw new LIB211TestclassException($message);
		}
	}
	
	/**
	 * Assert that $left will be greater than $right
	 * @param mixed $left
	 * @param mixed $right
	 * @throws LIB211TestclassException
	 */
	final public function assertGreaterThan($left,$right) {
		$message = 'assertGreaterEquals failed: "'.$this->_formatType($left).'" not greater than "'.$this->_formatType($right).'"';
		if (!($left >= $right)) { 
			throw new LIB211TestclassException($message);
		}
	}
		
	/**
	 * Assert that $left will be lesser $right
	 * @param mixed $left
	 * @param mixed $right
	 * @throws LIB211TestclassException
	 */
	final public function assertLesser($left,$right) {
		$message = 'assertLesser failed: "'.$this->_formatType($left).'" not lesser "'.$this->_formatType($right).'"';
		if (!($left < $right)) { 
			throw new LIB211TestclassException($message);
		}
	}
	
	/**
	 * Assert that $left will be lesser than $right
	 * @param mixed $left
	 * @param mixed $right
	 * @throws LIB211TestclassException
	 */
	final public function assertLesserThan($left,$right) {
		$message = 'assertLesserThan failed: "'.$this->_formatType($left).'" not lesser than "'.$this->_formatType($right).'"';
		if (!($left <= $right)) { 
			throw new LIB211TestclassException($message);
		}
	}
	
	/**
	 * Assert that $left will be not equals to $right
	 * @param mixed $left
	 * @param mixed $right
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	final public function assertNotEquals($left,$right,$checkType=TRUE) {
		$message = 'assertNotEquals failed: "'.$this->_formatType($left).'" equals "'.$this->_formatType($right).'"';
		if ($checkType) {
			if ($left === $right) {
				throw new LIB211TestclassException($message);
			}
		}
		else {
			if ($left == $right) { 
				throw new LIB211TestclassException($message);
			}
		}
	}
	
	/**
	 * Assert that $test will be not false
	 * @param mixed $test
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	final public function assertNotFalse($test,$checkType=TRUE) {
		$message = 'assertNotFalse failed: "'.$this->_formatType($test).'" is "FALSE"';
		if ($checkType) {
			if ($test === FALSE) {
				throw new LIB211TestclassException($message);
			}
		}
		else {
			if ($test == FALSE) { 
				throw new LIB211TestclassException($message);
			}
		}
	}
	
	/**
	 * Assert that $left will be not greater $right
	 * @param mixed $left
	 * @param mixed $right
	 * @throws LIB211TestclassException
	 */
	final public function assertNotGreater($left,$right) {
		$message = 'assertNotGreater failed: "'.$this->_formatType($left).'" greater "'.$this->_formatType($right).'"';
		if ($left > $right) { 
			throw new LIB211TestclassException($message);
		}
	}
	
	/**
	 * Assert that $left will be not greater than $right
	 * @param mixed $left
	 * @param mixed $right
	 * @throws LIB211TestclassException
	 */
	final public function assertNotGreaterThan($left,$right) {
		$message = 'assertNotGreaterThan failed: "'.$this->_formatType($left).'" greater than "'.$this->_formatType($right).'"';
		if ($left >= $right) { 
			throw new LIB211TestclassException($message);
		}
	}
	
	/**
	 * Assert that $left will be not lesser $right
	 * @param mixed $left
	 * @param mixed $right
	 * @throws LIB211TestclassException
	 */
	final public function assertNotLesser($left,$right) {
		$message = 'assertNotLesser failed: "'.$this->_formatType($left).'" lesser "'.$this->_formatType($right).'"';
		if ($left < $right) { 
			throw new LIB211TestclassException($message);
		}
	}
	
	/**
	 * Assert that $left will be not lesser than $right
	 * @param mixed $left
	 * @param mixed $right
	 * @throws LIB211TestclassException
	 */
	final public function assertNotLesserThan($left,$right) {
		$message = 'assertNotLesserThan failed: "'.$this->_formatType($left).'" lesser than "'.$this->_formatType($right).'"';
		if ($left <= $right) { 
			throw new LIB211TestclassException($message);
		}
	}
	
	/**
	 * Assert that $test will be not null
	 * @param mixed $test
	 * @param mixed $checkType
	 * @throws LIB211TestclassException
	 */
	final public function assertNotNull($test,$checkType=TRUE) {
		$message = 'assertNotNull failed: "'.$this->_formatType($test).'" is "NULL"';
		if ($checkType) {
			if ($test === NULL) {
				throw new LIB211TestclassException($message);
			}
		}
		else {
			if ($test == NULL) { 
				throw new LIB211TestclassException($message);
			}
		}
	}
	
	/**
	 * Assert that $test will be not true
	 * @param mixed $test
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	final public function assertNotTrue($test,$checkType=TRUE) {
		$message = 'assertNotTrue failed: "'.$this->_formatType($test).'" is "TRUE"';
		if ($checkType) {
			if ($test === TRUE) {
				throw new LIB211TestclassException($message);
			}
		}
		else {
			if ($test == TRUE) { 
				throw new LIB211TestclassException($message);
			}
		}
	}
	
	/**
	 * Assert that $test will be null
	 * @param mixed $test
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	final public function assertNull($test,$checkType=TRUE) {
		$message = 'assertNull failed: "'.$this->_formatType($test).'" is not "NULL"';
		if ($checkType) {
			if ($test !== NULL) {
				throw new LIB211TestclassException($message);
			}
		}
		else {
			if ($test != NULL) { 
				throw new LIB211TestclassException($message);
			}
		}
	}
	
	/**
	 * Assert that $test will be true
	 * @param mixed $test
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	final public function assertTrue($test,$checkType=TRUE) {
		$message = 'assertTrue failed: "'.$this->_formatType($test).'" is not "TRUE"';
		if ($checkType) {
			if ($test !== TRUE) {
				throw new LIB211TestclassException($message);
			}
		}
		else {
			if ($test != TRUE) { 
				throw new LIB211TestclassException($message);
			}
		}
	}
		
	/**
	 * Assert that a comparisation is not false, null, or empty
	 * @param mixed $test
	 * @throws LIB211TestclassException
	 */
	final public function assertThat($test) {
		$message = 'assertThat failed: "'.$this->_formatType($test).'"';
		if (
				$test === FALSE OR 
				$test === NULL OR 
				$test === 0 OR 
				$test === 0.0 OR 
				$test === '' OR 
				$test === array()  OR
				$test === new stdClass()  OR
				empty($test)
		) {
			throw new LIB211TestclassException($message);
		}
	}
	
	/**
	 * Execute before each test method
	 */
	public function setPrefix() {
	}
	
	/**
	 * Execute before all test methods
	 */
	public function setPrefixAll() {
	}
	
	/**
	 * Execute after each test method
	 */
	public function setSuffix() {
	}
	
	/**
	 * Execute after all test methods
	 */
	public function setSuffixAll() {
	}
	
}

/**
 * LIB211 Testclass Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211TestclassException extends LIB211BaseException{
}