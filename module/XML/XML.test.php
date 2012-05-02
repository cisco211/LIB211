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
	require_once(LIB211_ROOT.'/module/XML/XML.class.php');
}

/**
 * LIB211 XML Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211XMLTest extends LIB211Testclass {
	
	/**
	 * Returns sample data $sample as $type
	 * @param string $type
	 * @param integer $sample
	 * @return string|array
	 */
	private function _dataProvider($type,$sample=NULL) {
		$data = '';
		switch ($type) {
			case 'xml':
				switch ($sample) {
					case 1:
						$data =
							'<?xml version="1.0" encoding="UTF-8"?>'.EOL. 
							'<d1>'.EOL.
							'	<d2>'.EOL.
							'		<d3>value</d3>'.EOL.
							'	</d2>'.EOL.
							'</d1>'.EOL
						;
					break;
					default:
						$data =
							'<?xml version="1.0" encoding="UTF-8"?>'.EOL.
							'<root data="roottest">'.EOL.
							'	<sub data="subattrtest">'.EOL.
							'		<content><![CDATA[CDATATest]]></content>'.EOL.
							'		<content data="content2test">Content2Test</content>'.EOL.
							'		<content data="content3test">&lt;entity&gt;&amp;</content>'.EOL.
							'	</sub>'.EOL.
							'</root>'.EOL
						;
					break;
				}
			break;
			case 'array':
				switch ($sample) {
					case 1:
						$data = array(
							'd1'=>array(
								'd2'=>array(
									'd3'=>'value'
								)
							)
						);
					break;
					default:
						$data = array(
							'root'=>array(
								'sub'=>array(
									'content'=>array(
										0=>'CDATATest',
										1=>array(
											'@attributes'=>array(
												'data'=>'content2test'
											),
											'@value'=>'Content2Test'
										),
										2=>array(
											'@attributes'=>array(
												'data'=>'content3test'
											),
											'@value' => '&lt;entity&gt;&amp;'
										)
									),
									'@attributes'=>array(
										'data'=>'subattrtest'
									)
								),
								'@attributes'=>array(
									'data'=>'roottest'
								)
							)
						);
					break;
				}
			break;
		}
		return $data;
	}
	
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
		$this->xml = new LIB211XML();
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
		unset($this->xml);
	}

	/**
	 * Execute afater all methods
	 */
	public function setSuffixAll() {
	}
	
	/**
	 * Test import() method
	 */
	public function testImport() {
		$this->assertEquals($this->_dataProvider('array'),$this->xml->import($this->_dataProvider('xml')));
		$this->assertEquals($this->_dataProvider('array',1),$this->xml->import($this->_dataProvider('xml',1)));
	}
	
	/**
	 * Test export() method
	 */
	public function testExport() {
		$this->xml->import($this->_dataProvider('xml'));
		$this->assertEquals(str_replace(array('<![CDATA[',']]>'),'',$this->_dataProvider('xml')),$this->xml->export());
		$this->xml->import($this->_dataProvider('xml',1));
		$this->assertEquals(str_replace(array('<![CDATA[',']]>'),'',$this->_dataProvider('xml',1)),$this->xml->export());
	}
	
	/**
	 * Test root() method
	 */
	public function testRoot() {
		$this->assertEquals('',$this->xml->root());
		$this->xml->import($this->_dataProvider('xml'));
		$this->assertEquals('root',$this->xml->root());
		$this->xml->import($this->_dataProvider('xml',1));
		$this->assertEquals('d1',$this->xml->root());
	}
	
}
