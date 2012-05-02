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
	require_once(LIB211_ROOT.'/module/Mail/Mail.class.php');
}

/**
 * LIB211 Mail Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211MailTest extends LIB211Testclass {
	
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
		$this->mail = new LIB211Mail();
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
		unset($this->mail);
	}

	/**
	 * Execute afater all methods
	 */
	public function setSuffixAll() {
	}

	/**
	 * Test attachment() method
	 */
	public function testAttachment() {
		$this->assertTrue($this->mail->attachment(LIB211_ROOT.'/module/Mail/test/attachment.txt'));
		$status = $this->mail->__status();
		$this->assertEquals($status['file'][1]['name'],'attachment.txt');
	}
	
	/**
	 * Test blindcopy() method
	 */
	public function testBlindcopy() {
		$this->assertTrue($this->mail->blindcopy('test@example.com'));
		$status = $this->mail->__status();
		$this->assertEquals($status['bcc'],'test@example.com');
	}
	
	/**
	 * Test carboncopy() method
	 */
	public function testCarboncopy() {
		$this->assertTrue($this->mail->carboncopy('test@example.com'));
		$status = $this->mail->__status();
		$this->assertEquals($status['cc'],'test@example.com');
	}
	
	/**
	 * Test flush() method
	 */
	public function testFlush() {
		$this->assertTrue($this->mail->flush());
		$status = $this->mail->__status();
		$this->assertEquals($status['addheader'],'');
		$this->assertEquals($status['bcc'],'');
		$this->assertEquals($status['cc'],'');
		$this->assertEquals($status['file'],array());
		$this->assertEquals($status['filecount'],0);
		$this->assertEquals($status['from'],'');
		$this->assertEquals($status['header'],'');
		$this->assertEquals($status['maildata'],'');
		$this->assertEquals($status['message'],'');
		$this->assertEquals($status['reply'],'');
		$this->assertEquals($status['subject'],'');
		$this->assertEquals($status['to'],'');
		$this->assertEquals($status['type'],'');
	}
	
	/**
	 * Test from() method
	 */
	public function testFrom() {
		$this->assertTrue($this->mail->from('test@example.com'));
		$status = $this->mail->__status();
		$this->assertEquals($status['from'],'test@example.com');
	}
	
	/**
	 * Test header() method
	 */
	public function testHeader() {
		$this->assertTrue($this->mail->header('x-testheader','test'));
		$status = $this->mail->__status();
		$this->assertEquals($status['addheader'],"x-testheader: test\r\n");
	}
	
	/**
	 * Test message() method
	 */
	public function testMessage() {
		$this->assertTrue($this->mail->message('Test','text/plain'));
		$status = $this->mail->__status();
		$this->assertEquals($status['message'],'Test');
		$this->assertEquals($status['type'],'text/plain');
	}
	
	/**
	 * Test reply() method
	 */
	public function testReply() {
		$this->assertTrue($this->mail->reply('test@example.com'));
		$status = $this->mail->__status();
		$this->assertEquals($status['reply'],'test@example.com');
	}
	
	/**
	 * Test send() method
	 */
	public function testSend() {
		$result = $this->mail->send(TRUE);
		$status = $this->mail->__status();
		$this->assertEquals($result,$status['maildata']);
	}
	
	/**
	 * Test subject() method
	 */
	public function testSubject() {
		$this->assertTrue($this->mail->subject('Test'));
		$status = $this->mail->__status();
		$this->assertEquals($status['subject'],'Test');
	}
	
	/**
	 * Test to() method
	 */
	public function testTo() {
		$this->assertTrue($this->mail->to('test@example.com'));
		$status = $this->mail->__status();
		$this->assertEquals($status['to'],'test@example.com');
	}
	
}
