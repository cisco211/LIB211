<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/Random/Random.class.php');
}

/**
 * Enter description here ...
 * @author ts
 *
 */
class LIB211RandomTest extends LIB211Testclass {

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
		$this->random = new LIB211Random();
	}
	
	/** 
	 * Execute after each test method
	 */
	public function setSuffix() {
	}
	
	/**
	 * Execute after all methods
	 */
	public function setSuffixAll() {
		unset($this->random);
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetArchitecture() {
		$this->assertEquals(PHP_INT_SIZE*8,$this->random->getArchitecture());
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetLoopMaxRuns() {
		$this->assertEquals(10,$this->random->getLoopMaxRuns());
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomBoolean() {
		$boolean = $this->random->getRandomBoolean();
		if ($boolean) $this->assertTrue($boolean);
		else $this->assertFalse($boolean);
	}

	/**
	 * Enter description here ...
	 */
	public function testGetRandomFloat() {
	}

	/**
	 * Enter description here ...
	 */
	public function testGetRandomFloatNegative() {
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomFloatPositive() {
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomGeohash() {
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomInteger() {
		$this->assertEquals(0,$this->random->getRandomInteger(0,0));
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomIntegerNegative() {
		$this->assertEquals(-1,$this->random->getRandomIntegerNegative(-1,-1));
	}

	/**
	 * Enter description here ...
	 */
	public function testGetRandomIntegerPositive() {
		$this->assertEquals(1,$this->random->getRandomIntegerPositive(1,1));
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomLatitude() {
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomLongitude() {
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomNull() {
		$this->assertEquals(NULL,$this->random->getRandomNull());
		$this->assertEquals('',$this->random->getRandomNull('string'));
		$this->assertEquals(0,$this->random->getRandomNull('integer'));
		$this->assertEquals(0.0,$this->random->getRandomNull('float'));
		$this->assertEquals(array(),$this->random->getRandomNull('array'));
	}
	
	public function testGetRandomString() {
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRandomTimestamp() {
		$this->assertEquals(0,$this->random->getRandomTimestamp(0,0));
		$this->assertEquals(10,$this->random->getRandomTimestamp(10,10));
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRangeIntegerMax() {
		switch ($this->random->getArchitecture()) {
			case 8:
				$this->assertEquals(127,$this->random->getRangeIntegerMax());
			break;
			case 16:
				$this->assertEquals(32767,$this->random->getRangeIntegerMax());
			break;
			case 32:
				$this->assertEquals(2147483647,$this->random->getRangeIntegerMax());
			break;
			case 64:
				$this->assertEquals(9223372036854775807,$this->random->getRangeIntegerMax());
			break;
		}
	}
	
	/**
	 * Enter description here ...
	 */
	public function testGetRangeIntegerMin() {
		switch ($this->random->getArchitecture()) {
			case 8:
				$this->assertEquals(-127,$this->random->getRangeIntegerMin());
			break;
			case 16:
				$this->assertEquals(-32767,$this->random->getRangeIntegerMin());
			break;
			case 32:
				$this->assertEquals(-2147483647,$this->random->getRangeIntegerMin());
			break;
			case 64:
				$this->assertEquals(-9223372036854775807,$this->random->getRangeIntegerMin());
			break;
		}
	}
		
	/**
	 * Enter description here ...
	 */
	public function testGetRangeTimestampMax() {
		$this->assertEquals(2147483647,$this->random->getRangeTimestampMax());
	}
	
	/**
	 * Enter description here ...
	 */
	public function testSetLoopMaxRuns() {
		$this->assertEquals(10,$this->random->getLoopMaxRuns());
		$this->assertTrue($this->random->setLoopMaxRuns(5));
		$this->assertEquals(5,$this->random->getLoopMaxRuns());
	}
	
	/**
	 * Enter description here ...
	 */
	public function testSetRangeInteger16Bit() {
		if ($this->random->getArchitecture() >= 16) {
			$this->assertTrue($this->random->setRangeInteger16Bit());
			$this->assertEquals(32767,$this->random->getRangeIntegerMax());
			$this->assertEquals(-32767,$this->random->getRangeIntegerMin());
		}
	}
	
	/**
	 * Enter description here ...
	 */
	public function testSetRangeInteger32Bit() {
		if ($this->random->getArchitecture() >= 32) {
			$this->assertTrue($this->random->setRangeInteger32Bit());
			$this->assertEquals(2147483647,$this->random->getRangeIntegerMax());
			$this->assertEquals(-2147483647,$this->random->getRangeIntegerMin());
		}
	}
	
	/**
	 * Enter description here ...
	 */
	public function testSetRangeInteger64Bit() {
		if ($this->random->getArchitecture() >= 64) {
			$this->assertTrue($this->random->setRangeInteger64Bit());
			$this->assertEquals(9223372036854775807,$this->random->getRangeIntegerMax());
			$this->assertEquals(-9223372036854775807,$this->random->getRangeIntegerMin());
		}
	}
	
	/**
	 * Enter description here ...
	 */
	public function testSetRangeInteger8Bit() {
		if ($this->random->getArchitecture() >= 8) {
			$this->assertTrue($this->random->setRangeInteger8Bit());
			$this->assertEquals(127,$this->random->getRangeIntegerMax());
			$this->assertEquals(-127,$this->random->getRangeIntegerMin());
		}
	}
	
	/**
	 * Enter description here ...
	 */
	public function testSetRangeIntegerMax() {
		$this->assertTrue($this->random->setRangeIntegerMax(5));
		$this->assertEquals(5,$this->random->getRangeIntegerMax());
	}
	
	/**
	 * Enter description here ...
	 */
	public function testSetRangeIntegerMin() {
		$this->assertTrue($this->random->setRangeIntegerMin(5));
		$this->assertEquals(5,$this->random->getRangeIntegerMin());
	}
	
	/**
	 * Enter description here ...
	 */
	public function testSetRangeTimestampMax() {
		$this->assertTrue($this->random->setRangeTimestampMax(5));
		$this->assertEquals(5,$this->random->getRangeTimestampMax());
	}

}