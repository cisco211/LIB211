<?php

class LIB211RandomTest extends LIB211Testclass {

	/** 
	 * 
	 */
	public function setPrefix() {
		$this->random = new LIB211Random();
	}
	
	/**
	 * 
	 */
	public function setSuffix() {
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
	public function testGetRandomNull() {
		$this->assertEquals(NULL,$this->random->getRandomNull());
		$this->assertEquals('',$this->random->getRandomNull('string'));
		$this->assertEquals(0,$this->random->getRandomNull('integer'));
		$this->assertEquals(0.0,$this->random->getRandomNull('float'));
		$this->assertEquals(array(),$this->random->getRandomNull('array'));
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