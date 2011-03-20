<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/Example/Example.class.php');
}

/**
 * LIB211 Example Testclass
 * 
 * @author C!$C0^211
 *
 */
class LIB211ExampleTest extends LIB211Testclass {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/** 
	 * Execute before each test method
	 */
	public function setPrefix() {
	}
	
	/**
	 * Execute before all methods
	 */
	public function setPrefixAll() {
		$this->example = new LIB211Example();
	}
	
	/** 
	 * Execute after each test method
	 */
	public function setSuffix() {
	}

	/**
	 * Execute afater all methods
	 */
	public function setSuffixAll() {
		unset($this->example);
	}

	/**
	 * Successful test #1
	 */
	public function testSuccess1() {
		$this->example->test();
		$this->assertTrue($this->example->test());
	}

	/**
	 * Failing test #1
	 */
	public function testFailed1() {
		$this->assertTrue($this->example->test());
		$this->assertEquals(1,0);
	}

	/**
	 * Successful test #2
	 */
	public function testSuccess2() {
		$this->assertTrue($this->example->test());
		$this->assertEquals(TRUE,TRUE);
	}
	
	/**
	 * Failing test #2
	 */
	public function testFailed2() {
		$this->assertTrue($this->example->test());
		$this->assertEquals(TRUE,FALSE);
	}

	/**
	 * Successful test #3
	 */
	public function testSuccess3() {
		$this->assertTrue($this->example->test());
		$this->assertEquals('tree','tree');
	}

	/**
	 * Failing test #3
	 */
	public function testFailed3() {
		$this->assertTrue($this->example->test());
		$this->assertEquals(array('foo','bar'),array('foo','bar','baz'));
	}
	
	/**
	 * Ignored test #1
	 * Empty tests are marked as "PASSED"
	 */
	public function testEmpty() {
	}
	
	/**
	 * Ignored test #2
	 * Ignored tests are marked as "PASSED"
	 * You can ignore a test through simply do a "return"
	 */
	public function testIgnored() {
		return;
		$this->assertTrue($this->example->test());
		$this->assertEquals(TRUE,FALSE);
	}
	
	/**
	 * Test all vailable assertions
	 */
	public function testAllAssertions() {
		assert(TRUE);
		$this->assertTrue($this->example->test());
		$this->assertEquals(1,1);
		$this->assertFalse(FALSE);
		$this->assertGreater(1,0);
		$this->assertGreaterThan(1,1);
		$this->assertLesser(0,1);
		$this->assertLesserThan(1,1);
		$this->assertNotEquals(0,1);
		$this->assertNotFalse(TRUE);
		$this->assertNotGreater(0,1);
		$this->assertNotGreaterThan(0,1);
		$this->assertNotLesser(1,0);
		$this->assertNotLesserThan(1,0);
		$this->assertNotNull(TRUE);
		$this->assertNotTrue(FALSE);
		$this->assertNull(NULL);
		$this->assertThat(array('foo','bar') === array('foo','bar'));
		$this->assertTrue(TRUE);
	}
	
}
