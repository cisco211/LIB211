<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/Geohash/Geohash.class.php');
}

/**
 * LIB211 Geohash Testclass
 * 
 * @author C!$C0^211
 *
 */
class LIB211GeohashTest extends LIB211Testclass {

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
		$this->geohash = new LIB211Geohash();
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
		unset($this->geohash);
	}
	
	/**
	 *  Test decodeInterval() method
	 */
	public function testDecodeInterval() {
		$decoded = $this->geohash->decodeInterval('s0');
		$this->assertEquals(0.0,$decoded[0][0]);
		$this->assertEquals(5.625,$decoded[0][1]);
		$this->assertEquals(0.0,$decoded[1][0]);
		$this->assertEquals(11.25,$decoded[1][1]);
	}
	
	/**
	 *  Test decode() method
	 */
	public function testDecode() {
		$decoded = $this->geohash->decode('s0000000000');
		$this->assertEquals(0.0,$decoded[0]);
		$this->assertEquals(0.0,$decoded[1]);
		$decoded = $this->geohash->decode('h0000000000');
		$this->assertEquals(-90.0,$decoded[0]);
		$this->assertEquals(0.0,$decoded[1]);
		$decoded = $this->geohash->decode('pbpbpbpbpbp');
		$this->assertEquals(-90.0,$decoded[0]);
		$this->assertEquals(180.0,$decoded[1]);
		$decoded = $this->geohash->decode('zzzzzzzzzzz');
		$this->assertEquals( 90.0,$decoded[0]);
		$this->assertEquals(180.0,$decoded[1]);
	}
	
	/**
	 * Test encode() method
	 */
	public function testEncode() {
		$this->assertEquals('s0000000000',$this->geohash->encode(  0,  0));
		$this->assertEquals('h0000000000',$this->geohash->encode(-90,  0));
		$this->assertEquals('pbpbpbpbpbp',$this->geohash->encode(-90,180));
		$this->assertEquals('zzzzzzzzzzz',$this->geohash->encode( 90,180));
	}
	
	/**
	 * Test neighbour() method
	 */
	public function testNeighbour() {
		$neighbours = $this->geohash->neighbour('pbpbpbpbpbp',1);
		$this->assertEquals('zzzzzzzzzzy',$neighbours[0]);
		$this->assertEquals('zzzzzzzzzzz',$neighbours[1]);
		$this->assertEquals('bpbpbpbpbpb',$neighbours[2]);
		$this->assertEquals('pbpbpbpbpbn',$neighbours[3]);
		$this->assertEquals('00000000000',$neighbours[4]);
		$this->assertEquals('pbpbpbpbpbq',$neighbours[5]);
		$this->assertEquals('pbpbpbpbpbr',$neighbours[6]);
		$this->assertEquals('00000000002',$neighbours[7]);
	}
	
}
