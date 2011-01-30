<?php

abstract class LIB211Testclass {
	
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
	
	public function setPrefix() {
	}
	
	public function setPrefixAll() {
	}
	
	public function setSuffix() {
	}
	
	public function setSuffixAll() {
	}
	
}

class LIB211TestclassException extends LIB211BaseException{
}