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
	require_once(LIB211_ROOT.'/module/Database/Database.class.php');
}

/**
 * LIB211 Database Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211DatabaseTest extends LIB211Testclass {
	
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
		$this->database = new LIB211Database();
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
		unset($this->database);
	}
	
}
