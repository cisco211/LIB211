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
	require_once(LIB211_ROOT.'/module/Permutate/Permutate.class.php');
}

/**
 * LIB211 Permutate Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211PermutateTest extends LIB211Testclass {

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
		$this->permutate = new LIB211Permutate();
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
		unset($this->permutate);
	}
	
	/** 
	 * Execute after all methods
	 */
	public function setSuffixAll() {
	}
	
	/**
	 * Test addList() method
	 */
	public function testAddList() {
	}
	
	/**
	 * Test addLists() method
	 */
	public function testAddLists() {
		
	}
	
	/**
	 * Test delList() method
	 */
	public function testDelList() {
	}
	
	/**
	 * Test delLists() method
	 */
	public function testDelLists() {
	}
	
	/**
	 * Test permutation with one dimension
	 */
	public function testDimension1() {
		$list = array('a');
		$test = array($list);
		$this->permutate->addList($list);
 		$this->assertEquals($test,$this->permutate->getResult());
	}

	/**
	 * Test permutation with two dimensions
	 */
	public function testDimension2() {
		$list1 = array('a','b');
		$list2 = array(1,2);
		$test = array(
			array('a',1),
			array('a',2),
			array('b',1),
			array('b',2)
		);
		$this->permutate->addList($list1);
		$this->permutate->addList($list2);
		$this->assertEquals($test,$this->permutate->getResult());
	}
	
	/**
	 * Test permutation with three dimensions
	 */
	public function testDimension3() {
		$list1 = array('a','b','c');
		$list2 = array(1,2,3);
		$list3 = array('alpha','beta','gamma');
		$test = array(
			array('a',1,'alpha'),
			array('a',1,'beta'),
			array('a',1,'gamma'),
			array('a',2,'alpha'),
			array('a',2,'beta'),
			array('a',2,'gamma'),
			array('a',3,'alpha'),
			array('a',3,'beta'),
			array('a',3,'gamma'),
			array('b',1,'alpha'),
			array('b',1,'beta'),
			array('b',1,'gamma'),
			array('b',2,'alpha'),
			array('b',2,'beta'),
			array('b',2,'gamma'),
			array('b',3,'alpha'),
			array('b',3,'beta'),
			array('b',3,'gamma'),
			array('c',1,'alpha'),
			array('c',1,'beta'),
			array('c',1,'gamma'),
			array('c',2,'alpha'),
			array('c',2,'beta'),
			array('c',2,'gamma'),
			array('c',3,'alpha'),
			array('c',3,'beta'),
			array('c',3,'gamma')
			);
		$this->permutate->addList($list1);
		$this->permutate->addList($list2);
		$this->permutate->addList($list3);
		$this->assertEquals($test,$this->permutate->getResult());
	}
	
	/**
	 * Test getList() method
	 */
	public function testGetList() {
	}
	
	/**
	 * Test getLists() method
	 */
	public function testGetLists() {
	}
	
	/**
	 * Test getResult() method
	 */
	public function testGetResult() {
	}

}