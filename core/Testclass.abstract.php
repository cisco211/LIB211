<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

/**
 * LIB211 Testclass abstraction
 * 
 * @author C!$C0^211
 *
 */
abstract class LIB211Testclass {
	
	/**
	 * Assert that $left will be equals to $right
	 * @param mixed $left
	 * @param mixed $right
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	protected function assertEquals($left,$right,$checkType=TRUE) {
		$message = 'assertEquals failed: "'.$left.'" not equals "'.$right.'"';
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
	protected function assertFalse($test,$checkType=TRUE) {
		$message = 'assertFalse failed: "'.$test.'" is not false';
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
	 * Assert that $left will be not equals to $right
	 * @param mixed $left
	 * @param mixed $right
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	protected function assertNotEquals($left,$right,$checkType=TRUE) {
		$message = 'assertNotEquals failed: "'.$left.'" equals "'.$right.'"';
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
	 * Assert that $test will be not null
	 * @param mixed $test
	 * @param mixed $checkType
	 * @throws LIB211TestclassException
	 */
	protected function assertNotNull($test,$checkType=TRUE) {
		$message = 'assertNotNull failed: "'.$test.'" is null';
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
	 * Assert that $test will be null
	 * @param mixed $test
	 * @param boolean $checkType
	 * @throws LIB211TestclassException
	 */
	protected function assertNull($test,$checkType=TRUE) {
		$message = 'assertNull failed: "'.$test.'" is not null';
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
	protected function assertTrue($test,$checkType=TRUE) {
		$message = 'assertTrue failed: "'.$test.'" is not true';
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
 *
 */
class LIB211TestclassException extends LIB211BaseException{
}