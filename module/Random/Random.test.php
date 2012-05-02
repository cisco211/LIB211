<?php
/**
 * @package LIB211
 */

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

/**
 * Include required files 
 */
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/Random/Random.class.php');
}

/**
 * LIB211 Random Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211RandomTest extends LIB211Testclass {

	/**
	 * How many loops for random in range tests
	 * @var integer
	 */
	private $loops = 100;
	
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
	 * Test getBoolean() method
	 */
	public function testGetBoolean() {
		$boolean = $this->random->getBoolean();
		$this->assertEquals(gettype($boolean),'boolean');
		if ($boolean === TRUE) $this->assertTrue($boolean);
		else $this->assertFalse($boolean);
	}
	
	/**
	 * Test getFloat() method
	 */
	public function testGetFloat() {
		
		// Test conversion between input and output
		$try = create_function('&$obj,$int,$precision',
			'$result = $obj->random->getFloat($int,$int,$precision);'.
			'if ($precision > 10) $precision = 10;'.
			'$obj->assertEquals((integer)$int,(integer)$result);'.
			'preg_match(\'/^([0-9\-]+)\.([0-9\-]+)$/\',$result,$m);'.
			'$obj->assertEquals($precision,strlen(@$m[2]));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,32,3);
		for($i=0;$i<$this->loops;$i++) $try($this,3235,7);
		for($i=0;$i<$this->loops;$i++) $try($this,-45,8);
		for($i=0;$i<$this->loops;$i++) $try($this,-2354,10);
		for($i=0;$i<$this->loops;$i++) $try($this,-1,11);

		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max,$precision',
			'$result = $obj->random->getFloat($min,$max,$precision);'.
			'$obj->assertLesserThan((integer)$result,$max);'.
			'$obj->assertGreaterThan((integer)$result,$min);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,-10,10,3);
		for($i=0;$i<$this->loops;$i++) $try($this,-11,11,5);
		for($i=0;$i<$this->loops;$i++) $try($this,-127,127,7);
		for($i=0;$i<$this->loops;$i++) $try($this,-1,235,2);
		for($i=0;$i<$this->loops;$i++) $try($this,-33,70,10);

	}
	
	/**
	 * Test getFloatNegative() method 
	 */
	public function testGetFloatNegative() {
		
		// Test conversion between input and output
		$try = create_function('&$obj,$int,$precision',
			'$result = $obj->random->getFloatNegative($int,$int,$precision);'.
			'if ($precision > 10) $precision = 10;'.
			'$obj->assertEquals((integer)$int,(integer)$result);'.
			'preg_match(\'/^([0-9\-]+)\.([0-9\-]+)$/\',$result,$m);'.
			'$obj->assertEquals($precision,strlen(@$m[2]));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,-32,3);
		for($i=0;$i<$this->loops;$i++) $try($this,-3235,7);
		for($i=0;$i<$this->loops;$i++) $try($this,-45,8);
		for($i=0;$i<$this->loops;$i++) $try($this,-2354,10);
		for($i=0;$i<$this->loops;$i++) $try($this,-1,11);

		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max,$precision',
			'$result = $obj->random->getFloatNegative($min,$max,$precision);'.
			'$obj->assertLesserThan((integer)$result,$min);'.
			'$obj->assertGreaterThan((integer)$result,$max);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,-10,-110,3);
		for($i=0;$i<$this->loops;$i++) $try($this,-11,-121,5);
		for($i=0;$i<$this->loops;$i++) $try($this,-127,-255,7);
		for($i=0;$i<$this->loops;$i++) $try($this,-1,-235,2);
		for($i=0;$i<$this->loops;$i++) $try($this,-33,-70,10);

	}
	
	/**
	 * Test getGeohash() method
	 */
	public function testGetGeohash() {
		
		// Check for string and 11 chars long
		$try = create_function('&$obj',
			'$result = $obj->random->getGeohash();'.
			'$obj->assertEquals(\'string\',gettype($result));'.
			'$obj->assertEquals(11,strlen($result));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this);
		
	}
	
	/**
	 * Test getFloatPositive() method
	 */
	public function testGetFloatPositive() {
		
		// Test conversion between input and output
		$try = create_function('&$obj,$int,$precision',
			'$result = $obj->random->getFloatPositive($int,$int,$precision);'.
			'if ($precision > 10) $precision = 10;'.
			'$obj->assertEquals((integer)$int,(integer)$result);'.
			'preg_match(\'/^([0-9\-]+)\.([0-9\-]+)$/\',$result,$m);'.
			'$obj->assertEquals($precision,strlen(@$m[2]));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,32,3);
		for($i=0;$i<$this->loops;$i++) $try($this,3235,7);
		for($i=0;$i<$this->loops;$i++) $try($this,45,8);
		for($i=0;$i<$this->loops;$i++) $try($this,2354,10);
		for($i=0;$i<$this->loops;$i++) $try($this,1,11);

		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max,$precision',
			'$result = $obj->random->getFloatPositive($min,$max,$precision);'.
			'$obj->assertLesserThan((integer)$result,$max);'.
			'$obj->assertGreaterThan((integer)$result,$min);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,10,110,3);
		for($i=0;$i<$this->loops;$i++) $try($this,11,121,5);
		for($i=0;$i<$this->loops;$i++) $try($this,127,255,7);
		for($i=0;$i<$this->loops;$i++) $try($this,1,235,2);
		for($i=0;$i<$this->loops;$i++) $try($this,33,70,10);

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
	
	public function testGetLatitude() {
		
		// Test conversion between input and output
		$try = create_function('&$obj,$int,$precision',
			'$result = $obj->random->getLatitude($int,$int,$precision);'.
			'if ($precision > 10) $precision = 10;'.
			'$obj->assertEquals((integer)$int,(integer)$result);'.
			'preg_match(\'/^([0-9\-]+)\.([0-9\-]+)$/\',$result,$m);'.
			'$obj->assertEquals($precision,strlen(@$m[2]));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,90,3);
		for($i=0;$i<$this->loops;$i++) $try($this,-13,7);
		for($i=0;$i<$this->loops;$i++) $try($this,-45,8);
		for($i=0;$i<$this->loops;$i++) $try($this,37,10);
		for($i=0;$i<$this->loops;$i++) $try($this,-90,11);

		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max,$precision',
			'$result = $obj->random->getLatitude($min,$max,$precision);'.
			'$obj->assertLesserThan((integer)$result,$max);'.
			'$obj->assertGreaterThan((integer)$result,$min);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,-90,90,3);
		for($i=0;$i<$this->loops;$i++) $try($this,-11,11,5);
		for($i=0;$i<$this->loops;$i++) $try($this,-42,42,7);
		for($i=0;$i<$this->loops;$i++) $try($this,-1,78,2);
		for($i=0;$i<$this->loops;$i++) $try($this,-33,70,10);
		
	}
	
	public function testGetLongitude() {

		// Test conversion between input and output
		$try = create_function('&$obj,$int,$precision',
			'$result = $obj->random->getLongitude($int,$int,$precision);'.
			'if ($precision > 10) $precision = 10;'.
			'$obj->assertEquals((integer)$int,(integer)$result);'.
			'preg_match(\'/^([0-9\-]+)\.([0-9\-]+)$/\',$result,$m);'.
			'$obj->assertEquals($precision,strlen(@$m[2]));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,180,3);
		for($i=0;$i<$this->loops;$i++) $try($this,-133,7);
		for($i=0;$i<$this->loops;$i++) $try($this,-45,8);
		for($i=0;$i<$this->loops;$i++) $try($this,95,10);
		for($i=0;$i<$this->loops;$i++) $try($this,-180,11);

		// Test, that you can really control the range
		$try = create_function('&$obj,$min,$max,$precision',
			'$result = $obj->random->getLongitude($min,$max,$precision);'.
			'$obj->assertLesserThan((integer)$result,$max);'.
			'$obj->assertGreaterThan((integer)$result,$min);'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,-180,180,3);
		for($i=0;$i<$this->loops;$i++) $try($this,-11,11,5);
		for($i=0;$i<$this->loops;$i++) $try($this,-42,42,7);
		for($i=0;$i<$this->loops;$i++) $try($this,-1,235,2);
		for($i=0;$i<$this->loops;$i++) $try($this,-133,170,10);

	}
	
	/**
	 * Test getLoopMaxRuns() method
	 */
	public function testGetLoopMaxRuns() {
		$this->assertEquals(10,$this->random->getLoopMaxRuns());
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
	 * Test getMD5() method
	 */
	public function testGetMd5() {

		// Check for string and 32 chars long
		$try = create_function('&$obj',
			'$result = $obj->random->getMd5();'.
			'$obj->assertEquals(\'string\',gettype($result));'.
			'$obj->assertEquals(32,strlen($result));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this);

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
	
	public function testGetString() {
		
		// Test conversion between input and output
		$try = create_function('&$obj,$string',
			'$obj->assertEquals($string,$obj->random->getString(array(),array($string)));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,'foo bar baz');
		for($i=0;$i<$this->loops;$i++) $try($this,'hello world');
		for($i=0;$i<$this->loops;$i++) $try($this,'12345678');
		for($i=0;$i<$this->loops;$i++) $try($this,'blabla');
		for($i=0;$i<$this->loops;$i++) $try($this,'ZZZZZZZZZZZZZZZZZZZZZ');

		// Test with wordlist
		$try = create_function('&$obj,$list',
			'$result = $obj->random->getString(array(),$list);'.
			'$obj->assertThat(in_array($result,$list));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,array('foo','bar','baz'));
		for($i=0;$i<$this->loops;$i++) $try($this,array('alpha','beta','gamma'));
		for($i=0;$i<$this->loops;$i++) $try($this,array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'));
		for($i=0;$i<$this->loops;$i++) $try($this,array('one','two','three','four','five','six','seven','eight','nine','zero'));
		for($i=0;$i<$this->loops;$i++) $try($this,array(0,1,2,3,4,5,6,7,8,9,0));
		
		// TODO: Create test for all these options :O
	}
	
	/**
	 * Test getStringList() method
	 */
	public function testGetStringList() {
		
		// Test for initial value
		$this->assertEquals(array('foo','bar','baz'),$this->random->getStringList());
		$this->assertEquals('foo',$this->random->getStringList(0));
		$this->assertEquals('bar',$this->random->getStringList(1));
		$this->assertEquals('baz',$this->random->getStringList(2));
		
		// Test with a new word list
		$this->random->setStringList(NULL);
		$this->random->setStringList(array(1,2,3));
		$this->assertEquals(1,$this->random->getStringList(0));
		$this->assertEquals(array(1,2,3),$this->random->getStringList());
		$this->assertEquals(2,$this->random->getStringList(1));
		$this->assertEquals(3,$this->random->getStringList(2));
	}
	
	/**
	 * Test getStringSequence() method
	 */
	public function testGetStringSequence() {
		
		// Test conversion between input and output
		$try = create_function('&$obj,$string',
			'$obj->assertEquals($string,$obj->random->getStringSequence(strlen($string),$string[0]));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,'0');
		for($i=0;$i<$this->loops;$i++) $try($this,'00');
		for($i=0;$i<$this->loops;$i++) $try($this,'1');
		for($i=0;$i<$this->loops;$i++) $try($this,'11');
		for($i=0;$i<$this->loops;$i++) $try($this,'ZZZZZZZZZZZZZZZZZZZZZ');

		// Test, that you can really control the range
		$try = create_function('&$obj,$size,$chrs',
			'$result = str_split($obj->random->getStringSequence($size,$chrs));'.
			'if ($chrs === NULL) $chrs = \'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ\';'.
			'$allowed = str_split($chrs);'.
			'foreach($result as $chr) {'.
			'	$obj->assertThat(in_array($chr,$allowed));'.
			'}'
		);
		for($i=0;$i<$this->loops;$i++) $try($this,8,NULL);
		for($i=0;$i<$this->loops;$i++) $try($this,128,'01');
		for($i=0;$i<$this->loops;$i++) $try($this,256,'a1b2c3d4e5f67890');
		for($i=0;$i<$this->loops;$i++) $try($this,4096,'1234567890abcdef-');
		for($i=0;$i<$this->loops;$i++) $try($this,8192,'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ .,-');
		
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

	/**
	 * Test setStringList() method
	 */
	public function testSetStringList() {
		
		// Test that nothing happened
		$this->random->setStringList();
		$this->assertEquals(array('foo','bar','baz'),$this->random->getStringList());
		
		// Test adding a value
		$this->random->setStringList('raz');
		$this->assertEquals(array('foo','bar','baz','raz'),$this->random->getStringList());
		$this->assertEquals('foo',$this->random->getStringList(0));
		$this->assertEquals('bar',$this->random->getStringList(1));
		$this->assertEquals('baz',$this->random->getStringList(2));
		$this->assertEquals('raz',$this->random->getStringList(3));

		// Test an empty list
		$this->random->setStringList(NULL);
		$this->assertEquals(array(),$this->random->getStringList());
		
		// Test creating new word list
		$this->random->setStringList(array(1,2,3));
		$this->assertEquals(1,$this->random->getStringList(0));
		$this->assertEquals(array(1,2,3),$this->random->getStringList());
		$this->assertEquals(2,$this->random->getStringList(1));
		$this->assertEquals(3,$this->random->getStringList(2));
	}
	
}