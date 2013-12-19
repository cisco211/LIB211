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
	require_once(LIB211_ROOT.'/module/Crontab/Crontab.class.php');
}

/**
 * LIB211 Crontab Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211CrontabTest extends LIB211Testclass {
	
	private function _entryDefault() {
		return array(
			'minute'=>NULL,
			'hour'=>NULL,
			'monthday'=>NULL,
			'month'=>NULL,
			'weekday'=>NULL,
			'command'=>NULL,
			'comment'=>NULL,
		);
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
		$this->crontab->reset();
		$this->addCommand = $this->_entryDefault();
		$this->addCommand['minute'] = '1';
		$this->addCommand['hour'] = '1';
		$this->addCommand['monthday'] = '1';
		$this->addCommand['month'] = '1';
		$this->addCommand['weekday'] = '1';
		$this->addCommand['command'] ='/dev/null';
		$this->addComment = $this->_entryDefault();
		$this->addComment['comment'] = 'test';
		$this->editCommand = $this->_entryDefault();
		$this->editCommand['minute'] = '2';
		$this->editCommand['hour'] = '2';
		$this->editCommand['monthday'] = '2';
		$this->editCommand['month'] = '2';
		$this->editCommand['weekday'] = '2';
		$this->editCommand['command'] ='/dev/null';
		$this->editComment = $this->_entryDefault();
		$this->editComment['comment'] = 'test2';
	}
	
	/**
	 * Execute before all methods
	 */
	public function setPrefixAll() {
		$this->crontab = new LIB211Crontab();
	}
	
	/** 
	 * Execute after each test method
	 */
	public function setSuffix() {
		$this->crontab->reset();
	}

	/**
	 * Execute afater all methods
	 */
	public function setSuffixAll() {
		unset($this->crontab);
	}

	/**
	 * Test add method
	 */
	public function testAdd() {
		$this->assertEquals($this->crontab->add($this->addCommand),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->addCommand));
		$this->crontab->reset();
		$this->assertEquals($this->crontab->add($this->addComment),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->addComment));
	}
	
	/**
	 * Test addCommand method
	 */
	public function testAddCommand() {
		$this->assertEquals($this->crontab->addCommand('1','1','1','1','1','/dev/null'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->addCommand));
	}
	
	/**
	 * Test addComment method
	 */
	public function testAddComment() {
		$this->assertEquals($this->crontab->addComment('test'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->addComment));
	}
	
	/**
	 * Test addLine method
	 */
	public function testAddLine() {
		$this->assertEquals($this->crontab->addLine('1 1 1 1 1 /dev/null'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->addCommand));
		$this->crontab->reset();
		$this->assertEquals($this->crontab->addLine('# test'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->addComment));
	}
	
	/**
	 * Test delete method
	 */
	public function testDelete() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->delete(0),TRUE);
	}
	
	/**
	 * Test delete command method
	 */
	public function testDeleteCommand() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->deleteCommand('/dev/null'),TRUE);
	}
	
	/**
	 * Test deleteCommandLine method
	 */
	public function testDeleteCommandLine() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->deleteCommandLine('* * * * * /dev/null'),TRUE);
	}
	
	/**
	 * Test deleteComment method
	 */
	public function testDeleteComment() {
		$this->crontab->add($this->addComment);
		$this->assertEquals($this->crontab->deleteComment('test'),TRUE);
	}
	
	/**
	 * Test deleteLine method
	 */
	public function testDeleteLine() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->deleteLine('1 1 1 1 1 /dev/null'),TRUE);
		$this->crontab->reset();
		$this->crontab->add($this->addComment);
		$this->assertEquals($this->crontab->deleteLine('# test'),TRUE);
	}
	
	/**
	 * Test edit method
	 */
	public function testEdit() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->edit(0,$this->editCommand),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->editCommand));
		$this->crontab->reset();
		$this->crontab->add($this->addComment);
		$this->assertEquals($this->crontab->edit(0,$this->editComment),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->editComment));
	}
	
	/**
	 * Test editCommand method
	 */
	public function testEditCommand() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->editCommand('2','2','2','2','2','/dev/null'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->editCommand));
	}
	
	/**
	 * Test editCommandLine method
	 */
	public function testEditCommandLine() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->editCommandLine('2 2 2 2 2 /dev/null'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->editCommand));
	}
	
	/**
	 * Test editComment method
	 */
	public function testEditComment() {
		$this->crontab->add($this->addComment);
		$this->assertEquals($this->crontab->editComment(0,'test2'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->editComment));
	}
	
	/**
	 * Test editLine method
	 */
	public function testEditLine() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->editLine(0,'2 2 2 2 2 /dev/null'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->editCommand));
		$this->crontab->reset();
		$this->crontab->add($this->addComment);
		$this->assertEquals($this->crontab->editLine(0,'# test2'),TRUE);
		$this->assertEquals($this->crontab->findAll(),array(0=>$this->editComment));
	}
	
	/**
	 * Test find method
	 */
	public function testFind() {
		$this->crontab->add($this->addCommand);
		$this->crontab->add($this->addComment);
		$this->crontab->add($this->editComment);
		$this->assertEquals($this->crontab->find(),array(
			0=>$this->addCommand,
			1=>$this->addComment,
			2=>$this->editComment,
		));
	}
	
	/**
	 * Test findAll method
	 */
	public function testFindAll() {
		$this->crontab->add($this->addCommand);
		$this->crontab->add($this->addComment);
		$this->crontab->add($this->editComment);
		$this->assertEquals($this->crontab->findAll(),array(
			0=>$this->addCommand,
			1=>$this->addComment,
			2=>$this->editComment,
		));
	}
	
	/**
	 * Test findCommand method
	 */
	public function testFindCommand() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->findCommand('/dev/null'),array(
			0=>$this->addCommand,
		));
	}
	
	/**
	 * Test findComment method
	 */
	public function testFindComment() {
		$this->crontab->add($this->addComment);
		$this->assertEquals($this->crontab->findComment('test'),array(
			0=>$this->addComment,
		));
	}
	
	/**
	 * Test findLine method
	 */
	public function testFindLine() {
		$this->crontab->add($this->addCommand);
		$this->assertEquals($this->crontab->findLine('1 1 1 1 1 /dev/null'),array(
			0=>$this->addCommand,
		));
		$this->crontab->reset();
		$this->crontab->add($this->addComment);
		$this->assertEquals($this->crontab->findLine('# test'),array(
			0=>$this->addComment,
		));
	}
	
	/**
	 * Test read method
	 */
	public function testRead() {
		return; // We dont test it here
	}
	
	/**
	 * Test reset method
	 */
	public function testReset() {
		$this->assertEquals($this->crontab->reset(),TRUE);
	}
	
	/**
	 * Test user method
	 */
	public function testUser() {
		$currentUser = trim(shell_exec('whoami'));
		$this->assertEquals($this->crontab->user(),$currentUser);
		$this->assertEquals($this->crontab->user('test'),'test');
		$this->assertEquals($this->crontab->user(),'test');
		$this->assertEquals($this->crontab->user($currentUser),$currentUser);
	}
	
	/**
	 * Test write method
	 */
	public function testWrite() {
		return; // We dont test it here
	}
	
	/**
	 * Test debugging
	 */
	public function testX() {
		return;
		ob_end_clean();
		$a = NULL;
		$b = NULL;
		$c = NULL;
		$d = NULL;
		$e = NULL;
		$f = NULL;
		$this->crontab->read();
		$a = $this->crontab->addLine('1 1 1 1 1 /dev/null');
		$b = $this->crontab->editCommandLine('2 2 2 2 2 /dev/null');
		$c = $this->crontab->deleteCommandLine('* * * * * /dev/null');
		$d = $this->crontab->find();
		$e = NULL;
		$f = NULL;
		#$this->crontab->write();
		print'<pre>';var_dump($a,$b,$c,$d,$e,$f);print'</pre>';
		exit();
	}
	
}
