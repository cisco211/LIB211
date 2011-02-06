<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

/**
 * LIB211 Scoper Testclass
 * 
 * @author C!$C0^211
 *
 */
class LIB211ScoperTest extends LIB211Testclass {
	
	/**
	 * Execute before each test method
	 */
	public function setPrefix() {
		$this->scoper = new LIB211Scoper();
	}
	
	/**
	 * Execute after each test method
	 */
	public function setSuffix() {
		unset($this->scoper);
	}
	
	/**
	 * Test get() method
	 */
	public function testGet() {
		$this->assertEquals(NULL,$this->scoper->get('foo'));
		$this->assertEquals('bar',$this->scoper->get('foo','bar'));
		$this->scoper->set('foo','foo');
		$this->assertEquals('foo',$this->scoper->get('foo','bar'));
	}
	
	/**
	 * Test run() method
	 */
	public function testRun() {
		// Not testable
	}
	
	/**
	 * Test set() method
	 */
	public function testSet() {
		$this->scoper->set('foo','foo');
		$this->assertEquals('foo',$this->scoper->get('foo','baz'));
		$this->scoper->set('foo','bar');
		$this->assertEquals('bar',$this->scoper->get('foo','baz'));
	}
	
}