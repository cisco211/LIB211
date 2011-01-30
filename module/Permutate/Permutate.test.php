<?php

class LIB211PermutateTest extends LIB211Testclass {
	
	public function setPrefix() {
		$this->permutate = new LIB211Permutate();
	}
	
	public function setSuffix() {
		unset($this->permutate);
	}
	
	public function testAddList() {
	}
	
	public function testAddLists() {
	}
	
	public function testDelList() {
	}
	
	public function testDelLists() {
	}
	
	public function testDimension1() {
		$list = array('a');
		$test = array($list);
		$this->permutate->addList($list);
 		$this->assertEquals($test,$this->permutate->getResult());
	}

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
	
	public function testGetList() {
	}
	
	public function testGetLists() {
	}
	
	public function testGetResult() {
	}
}