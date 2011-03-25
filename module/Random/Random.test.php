<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/Random/Random.class.php');
}

/**
 * LIB211 Random Testclass
 * 
 * @author C!$C0^211
 *
 */
class LIB211RandomTest extends LIB211Testclass {

	/**
	 * How many loops for random in range tests
	 * @var integer
	 */
	private $loops = 10;
	
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
		$this->random = new LIB211Random();
	}
	
	/** 
	 * Execute before all methods
	 */
	public function setPrefixAll() {
	}
	
	/** 
	 * Execute after each test method
	 */
	public function setSuffix() {
		unset($this->random);
	}
	
	/**
	 * Execute after all methods
	 */
	public function setSuffixAll() {
	}
	
	/**
	 * Test getArchitecture() method
	 */
	public function testGetArchitecture() {
		$this->assertEquals(PHP_INT_SIZE*8,$this->random->getArchitecture());
	}
	
	/**
	 * Test getLoopMaxRuns() method
	 */
	public function testGetLoopMaxRuns() {
		$this->assertEquals(10,$this->random->getLoopMaxRuns());
	}
	
	/**
	 * Test getBoolean() method
	 */
	public function testGetBoolean() {
		$boolean = $this->random->getBoolean();
		$this->assertEquals(gettype($boolean),'boolean');
		if ($boolean === TRUE) $this->assertTrue($boolean);
		else $this->assertFalse($boolean);
	}
	
	/**
	 * Test getInteger() method
	 */
	public function testGetInteger() {

		// Test conversion between input and output
		$try = create_function('&$obj,$int',
			'$obj->assertEquals($int,$obj->random->getInteger($int,$int));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,3);
		for($i=0;$i<$this->loops;$i++) $try($this,643);
		for($i=0;$i<$this->loops;$i++) $try($this,22332443);

		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max',
			'$result = $obj->random->getInteger($min,$max);'.
			'$obj->assertLesserThan($result,$max);'.
			'$obj->assertGreaterThan($result,$min);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,-10,10);
		for($i=0;$i<$this->loops;$i++) $try($this,-11,23);
		for($i=0;$i<$this->loops;$i++) $try($this,-127,127);

	}
	
	/**
	 * Test getIntegerNegative() method
	 */
	public function testGetIntegerNegative() {

		// Test conversion between input and output
		$try = create_function('&$obj,$int',
			'$obj->assertEquals($int,$obj->random->getIntegerNegative($int,$int));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,-5);
		for($i=0;$i<$this->loops;$i++) $try($this,-363);
		for($i=0;$i<$this->loops;$i++) $try($this,-233243);
		
		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max',
			'$result = $obj->random->getIntegerNegative($min,$max);'.
			'$obj->assertLesserThan($result,$min);'.
			'$obj->assertGreaterThan($result,$max);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,-5,-10);
		for($i=0;$i<$this->loops;$i++) $try($this,-231,-574);
		for($i=0;$i<$this->loops;$i++) $try($this,-24236,-27934);
	}

	/**
	 * Test getIntegerPositive() method
	 */
	public function testGetIntegerPositive() {

		// Test conversion between input and output
		$try = create_function('&$obj,$int',
			'$obj->assertEquals($int,$obj->random->getIntegerPositive($int,$int));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,7);
		for($i=0;$i<$this->loops;$i++) $try($this,234);
		for($i=0;$i<$this->loops;$i++) $try($this,76343);

		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max',
			'$result = $obj->random->getIntegerPositive($min,$max);'.
			'$obj->assertLesserThan($result,$max);'.
			'$obj->assertGreaterThan($result,$min);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,5,10);
		for($i=0;$i<$this->loops;$i++) $try($this,341,545);
		for($i=0;$i<$this->loops;$i++) $try($this,23782,42982);

	}
	
	/**
	 * Test getIPv4() method
	 */
	public function testGetIPv4() {

		// Test conversion between input and output
		$try = create_function('&$obj,$ip',
			'$obj->assertEquals($ip,$obj->random->getIPv4($ip,$ip));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,'0.0.0.0');
		for($i=0;$i<$this->loops;$i++) $try($this,'127.127.127.127');$idle;
		for($i=0;$i<$this->loops;$i++) $try($this,'255.255.255.255');$idle;
		
		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max',
			'$lMin = explode(\'.\',$min); foreach($lMin as $k => $v) $lMin[$k] = (integer)$v;'.
			'$lMax = explode(\'.\',$max); foreach($lMax as $k => $v) $lMax[$k] = (integer)$v;'.
			'$result = explode(\'.\',$obj->random->getIPv4($min,$max));'.
			'foreach ($result as $k => $v) { '.
			'	$result[$k] = (integer)$v;'.
			'	$obj->assertLesserThan($result[$k],$lMax[$k]);'.
			'	$obj->assertGreaterThan($result[$k],$lMin[$k]);'.
			'}'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,'10.10.0.1','10.10.254.254');
		for($i=0;$i<$this->loops;$i++) $try($this,'127.0.0.1','127.254.254.254');
		for($i=0;$i<$this->loops;$i++) $try($this,'192.168.0.1','192.168.0.254');
		for($i=0;$i<$this->loops;$i++) $try($this,'80.64.0.0','80.64.255.255');
		for($i=0;$i<$this->loops;$i++) $try($this,'240.0.0.0','240.255.255.255');
		
		}
	
	/**
	 * Test getIPv6() method
	 */
	public function testGetIPv6() {
		
		// Test conversion between input and output
		$try = create_function('&$obj,$ip',
			'$obj->assertEquals($ip,$obj->random->getIPv6($ip,$ip,FALSE));'.
			'$obj->assertEquals(strtoupper($ip),$obj->random->getIPv6(strtoupper($ip),strtoupper($ip),TRUE));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,'0000:0000:0000:0000:0000:0000:0000:0000');
		for($i=0;$i<$this->loops;$i++) $try($this,'0111:0111:0111:0111:0111:0111:0111:0111');
		for($i=0;$i<$this->loops;$i++) $try($this,'0123:4567:89ab:cdef:0123:4567:89ab:cdef');
		for($i=0;$i<$this->loops;$i++) $try($this,'0211:0211:0211:0211:0211:0211:0211:0211');
		for($i=0;$i<$this->loops;$i++) $try($this,'0808:0808:0808:0808:0808:0808:0808:0808');
		for($i=0;$i<$this->loops;$i++) $try($this,'0815:0815:0815:0815:0815:0815:0815:0815');
		for($i=0;$i<$this->loops;$i++) $try($this,'015f:015f:015f:015f:015f:015f:015f:015f');
		for($i=0;$i<$this->loops;$i++) $try($this,'8080:8080:8080:8080:8080:8080:8080:8080');
		for($i=0;$i<$this->loops;$i++) $try($this,'abcd:abcd:abcd:abcd:abcd:abcd:abcd:abcd');
		for($i=0;$i<$this->loops;$i++) $try($this,'efef:efef:efef:efef:efef:efef:efef:efef');
		for($i=0;$i<$this->loops;$i++) $try($this,'fedc:ba98:7654:3210:fedc:ba98:7654:3210');
		for($i=0;$i<$this->loops;$i++) $try($this,'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
				
		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max',
			'$lMin = explode(\':\',$min); foreach($lMin as $k => $v) $lMin[$k] = (integer)base_convert($v,16,10);'.
			'$lMax = explode(\':\',$max); foreach($lMax as $k => $v) $lMax[$k] = (integer)base_convert($v,16,10);'.
			'$result = explode(\':\',$obj->random->getIPv6($min,$max));'.
			'foreach ($result as $k => $v) { '.
			'	$result[$k] = (integer)base_convert($v,16,10);'.
			'	$obj->assertLesserThan($result[$k],$lMax[$k]);'.
			'	$obj->assertGreaterThan($result[$k],$lMin[$k]);'.
			'}'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,'0000:0000:0000:0000:0000:0000:0000:0000','ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
		for($i=0;$i<$this->loops;$i++) $try($this,'0000:0000:0000:0000:0000:0000:0000:0000','0001:0001:0001:0001:0001:0001:0001:0001');
		for($i=0;$i<$this->loops;$i++) $try($this,'0000:0000:0000:0000:0000:0000:0000:0000','aaaa:aaaa:aaaa:aaaa:aaaa:aaaa:aaaa:aaaa');
		for($i=0;$i<$this->loops;$i++) $try($this,'aaaa:aaaa:aaaa:aaaa:aaaa:aaaa:aaaa:aaaa','ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
		for($i=0;$i<$this->loops;$i++) $try($this,'fffe:fffe:fffe:fffe:fffe:fffe:fffe:fffe','ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
	}
	
	/**
	 * Test getNull() method
	 */
	public function testGetNull() {
		$this->assertEquals(NULL,$this->random->getNull());
		$this->assertEquals('',$this->random->getNull('string'));
		$this->assertEquals(0,$this->random->getNull('integer'));
		$this->assertEquals(0.0,$this->random->getNull('float'));
		$this->assertEquals(array(),$this->random->getNull('array'));
	}
	
	/**
	 * Test getTimestamp() method
	 */
	public function testGetTimestamp() {
	
		// Test conversion between input and output
		$try = create_function('&$obj,$int',
			'$obj->assertEquals($int,$obj->random->getTimestamp($int,$int));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,10);
		for($i=0;$i<$this->loops;$i++) $try($this,342);
		for($i=0;$i<$this->loops;$i++) $try($this,454342);

		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max',
			'$result = $obj->random->getTimestamp($min,$max);'.
			'$obj->assertLesserThan($result,$max);'.
			'$obj->assertGreaterThan($result,$min);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,0,100000);
		for($i=0;$i<$this->loops;$i++) $try($this,232,654);
		for($i=0;$i<$this->loops;$i++) $try($this,43234,76578);
		
	}
	
	/**
	 * Test getIntegerMax() method
	 */
	public function testGetIntegerMax() {
		switch ($this->random->getArchitecture()) {
			case 8:
				$this->assertEquals(127,$this->random->getIntegerMax());
			break;
			case 16:
				$this->assertEquals(32767,$this->random->getIntegerMax());
			break;
			case 32:
				$this->assertEquals(2147483647,$this->random->getIntegerMax());
			break;
			case 64:
				$this->assertEquals(9223372036854775807,$this->random->getIntegerMax());
			break;
		}
	}
	
	/**
	 * Test getIntegerMin() method
	 */
	public function testGetIntegerMin() {
		switch ($this->random->getArchitecture()) {
			case 8:
				$this->assertEquals(-127,$this->random->getIntegerMin());
			break;
			case 16:
				$this->assertEquals(-32767,$this->random->getIntegerMin());
			break;
			case 32:
				$this->assertEquals(-2147483647,$this->random->getIntegerMin());
			break;
			case 64:
				$this->assertEquals(-9223372036854775807,$this->random->getIntegerMin());
			break;
		}
	}
	
	/**
	 * Test setLoopMaxRuns() method
	 */
	public function testSetLoopMaxRuns() {
		$this->assertEquals(10,$this->random->getLoopMaxRuns());
		$this->assertTrue($this->random->setLoopMaxRuns(5));
		$this->assertEquals(5,$this->random->getLoopMaxRuns());
	}
	
	/**
	 * Test setInteger16Bit() method
	 */
	public function testSetInteger16Bit() {
		if ($this->random->getArchitecture() >= 16) {
			$this->assertTrue($this->random->setInteger16Bit());
			$this->assertEquals(32767,$this->random->getIntegerMax());
			$this->assertEquals(-32767,$this->random->getIntegerMin());
		}
	}
	
	/**
	 * Test setInteger32Bit() method
	 */
	public function testSetInteger32Bit() {
		if ($this->random->getArchitecture() >= 32) {
			$this->assertTrue($this->random->setInteger32Bit());
			$this->assertEquals(2147483647,$this->random->getIntegerMax());
			$this->assertEquals(-2147483647,$this->random->getIntegerMin());
		}
	}
	
	/**
	 * Test setInteger64Bit() method
	 */
	public function testSetInteger64Bit() {
		if ($this->random->getArchitecture() >= 64) {
			$this->assertTrue($this->random->setInteger64Bit());
			$this->assertEquals(9223372036854775807,$this->random->getIntegerMax());
			$this->assertEquals(-9223372036854775807,$this->random->getIntegerMin());
		}
	}
	
	/**
	 * Test setInteger8Bit() method
	 */
	public function testSetInteger8Bit() {
		if ($this->random->getArchitecture() >= 8) {
			$this->assertTrue($this->random->setInteger8Bit());
			$this->assertEquals(127,$this->random->getIntegerMax());
			$this->assertEquals(-127,$this->random->getIntegerMin());
		}
	}
	
	/**
	 * Test setIntegerMax() method
	 */
	public function testSetIntegerMax() {
		$this->assertTrue($this->random->setIntegerMax(5));
		$this->assertEquals(5,$this->random->getIntegerMax());
	}
	
	/**
	 * Test setIntegerMin() method
	 */
	public function testSetIntegerMin() {
		$this->assertTrue($this->random->setIntegerMin(5));
		$this->assertEquals(5,$this->random->getIntegerMin());
	}

}