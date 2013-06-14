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
	require_once(LIB211_ROOT.'/module/HTTPd/HTTPd.class.php');
}

/**
 * LIB211 HTTPd Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPdTest extends LIB211Testclass {
	
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
		$this->httpd = new LIB211HTTPd();
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
		unset($this->httpd);
	}
	
	/**
	 * Test htconf component
	 */
	public function testComponent_htconf() {
		
		// Component
		$htconf = $this->httpd->htconf();
		
		// Array data
		$array = array(
			array('text'=>'Example comment'),
			array('key'=>'rootKey','value'=>'rootValue'),
			array('sub'=>'IfDefine','expr'=>'mod_core.c','data'=>array(
				array('key'=>'subKey','value'=>'subValue'),
			)),
		);
		
		// Conf data
		$conf = 
			'# Example comment'.EOL.
			'rootKey rootValue'.EOL.
			'<IfDefine mod_core.c>'.EOL.
			chr(9).'subKey subValue'.EOL.
			'</IfDefine>'.EOL;
		
		// File
		$file = LIB211_ROOT.'/module/HTTPd/htconf.tmp';
		
		// Write file
		$this->assertEquals($htconf->write($file,$array),$conf);
		
		// Read file
		$this->assertEquals(json_encode($htconf->read($file)),json_encode($array));
		
		// Delete file
		$this->assertTrue(unlink($file));
	}
	
	/**
	 * Test htgroups component
	 */
	public function testComponent_htgroups() {
		
		// Component
		$htgroups = $this->httpd->htgroups();
		
		// File
		$file = LIB211_ROOT.'/module/HTTPd/htgroups.tmp';
		
		// Create file
		$this->assertTrue($htgroups->write($file,array()));
		
		// Add entry
		$this->assertTrue($htgroups->add($file,'Test','User'));
		
		// Read entry
		$this->assertEquals($htgroups->read($file),array('Test'=>array('User')));
		
		// Delete entry
		$this->assertTrue($htgroups->del($file,'Test','User'));
		
		// Delete file
		$this->assertTrue(unlink($file));
	}
	
	/**
	 * Test htpasswd component
	 */
	public function testComponent_htpasswd() {
		
		// Component
		$htpasswd = $this->httpd->htpasswd();
		
		// Hash algorithm
		$this->assertTrue($htpasswd->algorithm('none'));
		
		// File
		$file = LIB211_ROOT.'/module/HTTPd/htpasswd.tmp';
		
		// Create file
		$this->assertTrue($htpasswd->write($file,array()));
		
		// Add entry
		$this->assertTrue($htpasswd->add($file,'User','Password'));
		
		// Read entry
		$this->assertEquals($htpasswd->read($file),array('User'=>'Password'));
		
		// Delete entry
		$this->assertTrue($htpasswd->del($file,'User'));
		
		// Delete file
		$this->assertTrue(unlink($file));
	}
	
}
