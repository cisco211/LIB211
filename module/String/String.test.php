<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/String/String.class.php');
}

/**
 * LIB211 String Testclass
 * 
 * @author C!$C0^211
 *
 */
class LIB211StringTest extends LIB211Testclass {
	
	/**
	 * How many loops for repeating tests
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
	}
	
	/**
	 * Execute before all methods
	 */
	public function setPrefixAll() {
		$this->string = new LIB211String();
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
		unset($this->example);
	}
	
	/**
	 * Test byte2human() method
	 */
	public function testByte2human() {
		$this->assertEquals('1.00 Byte',$this->string->byte2human(1));
		$this->assertEquals('1.00 KByte',$this->string->byte2human(1024));
		$this->assertEquals('1.00 MByte',$this->string->byte2human(1048576));
		$this->assertEquals('309.19 MByte',$this->string->byte2human(324213342));
		$this->assertEquals('1.00 GByte',$this->string->byte2human(1073741824));
	}
	
	/**
	 * Test fill() method
	 */
	public function testFill() {
		$this->assertEquals('000',$this->string->fill('',3));
		$this->assertEquals('     ',$this->string->fill('',5,'l',' '));
		$this->assertEquals('1200000',$this->string->fill('12',7,'r','0'));
		$this->assertEquals(' x ',$this->string->fill('x ',3,'l',' '));
		$this->assertEquals('1.50000000',$this->string->fill('1.5',10,'r'));
	}
	
	/**
	 * Test find() method
	 */
	public function testFind() {
		$this->assertTrue($this->string->find('hello','hello world'));
		$this->assertFalse($this->string->find('l','hello world',10));
		$this->assertTrue($this->string->find('D','ABCDEFG'));
		$this->assertFalse($this->string->find('H','ABCDEFG'));
		$this->assertFalse($this->string->find('baz','foobar'));
	}
	
	/**
	 * Test indent() method
	 */
	public function testIndent() {
		$this->assertEquals("\t\t\t\t\t",$this->string->indent(5));
		$this->assertEquals('  ',$this->string->indent(2,' '));
		$this->assertEquals('xxxx',$this->string->indent(4,'x'));
		$this->assertEquals('.',$this->string->indent(1,'.'));
		$this->assertEquals('abababababab',$this->string->indent(6,'ab'));
	}
	
	/**
	 * Test rotx() method
	 */
	public function testRotx() {
		$this->assertEquals('uryyb jbeyq',$this->string->rotx('hello world',13));
		$this->assertEquals('nww jiz jih',$this->string->rotx('foo bar baz',8));
		$this->assertEquals('fqumf gjyf lfrrf',$this->string->rotx('alpha beta gamma',5));
		$this->assertEquals('zyp ehz escpp',$this->string->rotx('one two three',11));
		$this->assertEquals('nqtgo kruwo',$this->string->rotx('lorem ipsum',2));
	}
	
	/**
	 * Test md5apr1() method
	 */
	public function testMd5apr1() {
		$try = create_function('&$obj',
			'$result = $obj->string->md5apr1(time().microtime());'.
			'$obj->assertEquals(\'string\',gettype($result));'.
			'$obj->assertEquals(37,strlen($result));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this);
	}
	
	/**
	 * Test md5c211() method
	 */
	public function testMd5c211() {
		$try = create_function('&$obj',
			'$result = $obj->string->md5c211(time().microtime());'.
			'$obj->assertEquals(\'string\',gettype($result));'.
			'$obj->assertEquals(32,strlen($result));'
		);
		for($i=0;$i<$this->loops;$i++) $try($this);
	}
	
}
