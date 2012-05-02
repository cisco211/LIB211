<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/Counter/Counter.class.php');
}

/**
 * LIB211 Example Testclass
 * 
 * @author C!$C0^211
 *
 */
class LIB211CounterTest extends LIB211Testclass {
	
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
		$this->counter = new LIB211Counter();
		
		$this->dataMathAdd = array();
		
		//$this->dataMathAdd[] = array('','','');
		//                        X + Y = Z
		$this->dataMathAdd[] = array( '-1', '1', '0');
		$this->dataMathAdd[] = array( '0', '0', '0');
		$this->dataMathAdd[] = array( '0', '1', '1');
		$this->dataMathAdd[] = array( '1', '0', '1');
		$this->dataMathAdd[] = array( '1', '1', '2');
		$this->dataMathAdd[] = array( '1', '2', '3');
		$this->dataMathAdd[] = array( '2', '1', '3');
		$this->dataMathAdd[] = array( '3', '3', '6');
		$this->dataMathAdd[] = array( '4', '5', '9');
		$this->dataMathAdd[] = array( '5', '4', '9');
		$this->dataMathAdd[] = array( '5', '5','10');
		$this->dataMathAdd[] = array( '5', '6','11');
		$this->dataMathAdd[] = array( '6', '5','11');
		$this->dataMathAdd[] = array( '8', '9','17');
		$this->dataMathAdd[] = array( '9', '8','17');
		$this->dataMathAdd[] = array( '9', '9','18');
		$this->dataMathAdd[] = array( '9','10','19');
		$this->dataMathAdd[] = array('10', '9','19');
		$this->dataMathAdd[] = array('10','10','20');
		
		$this->dataMathAdd[] = array('0.0','0.1','0.1');
		$this->dataMathAdd[] = array('0.1','0.0','0.1');
		$this->dataMathAdd[] = array('0.1','0.1','0.2');
		
		$this->dataConversion = array();
		
		$this->dataConversion   ['0'] = array('NEGATIVE'=>FALSE, 'LOW'=>array(), 'HIGH'=>array(0));
		$this->dataConversion   ['1'] = array('NEGATIVE'=>FALSE, 'LOW'=>array(), 'HIGH'=>array(1));
		$this->dataConversion  ['12'] = array('NEGATIVE'=>FALSE, 'LOW'=>array(), 'HIGH'=>array(2,1));
		$this->dataConversion ['123'] = array('NEGATIVE'=>FALSE, 'LOW'=>array(), 'HIGH'=>array(3,2,1));
		
		$this->dataConversion   ['0.0']   = array('NEGATIVE'=>FALSE, 'LOW'=>array(0),     'HIGH'=>array(0));
		$this->dataConversion   ['1.1']   = array('NEGATIVE'=>FALSE, 'LOW'=>array(1),     'HIGH'=>array(1));
		$this->dataConversion  ['12.12']  = array('NEGATIVE'=>FALSE, 'LOW'=>array(1,2),   'HIGH'=>array(2,1));
		$this->dataConversion ['123.123'] = array('NEGATIVE'=>FALSE, 'LOW'=>array(1,2,3), 'HIGH'=>array(3,2,1));
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
		unset($this->counter);
	}

	public function testMathAdd() {
		foreach ($this->dataMathAdd as $equation) {
			$this->counter->setValue($equation[0]); 
			$this->counter->mathAdd($equation[1]); 
			$this->assertEquals($equation[2],$this->counter->getValue());
			//print $this->counter->getValue().'<br/>';
		}
	}
	
	public function testData2Type() {
		foreach ($this->dataConversion as $str => $ray) $this->assertEquals($str,$this->counter->data2Type($ray),FALSE);
	}
	
	public function testType2Data() {
		foreach ($this->dataConversion as $str => $ray) $this->assertEquals($ray,$this->counter->type2Data($str),FALSE);
	}
	
}
