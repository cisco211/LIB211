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
	require_once(LIB211_ROOT.'/module/AES/AES.class.php');
}

/**
 * LIB211 AES Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211AESTest extends LIB211Testclass {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Execute before all methods
	 */
	public function setPrefixAll() {
		$this->aes = new LIB211AES();
	}
	
	/**
	 * Execute afater all methods
	 */
	public function setSuffixAll() {
		$path = LIB211_ROOT.'/module/AES/test.dat';
		if (file_exists($path)) unlink($path);
		if (file_exists($path.'.aes211')) unlink($path.'.aes211');
		unset($this->aes);
	}

	/**
	 *  Test decrypt() method
	 */
	public function testDecrypt() {
		$path = LIB211_ROOT.'/module/AES/test.dat';
		$data = md5(time().microtime());
		$pwd = md5(time().microtime());
		file_put_contents($path,$data);
		$this->aes->encrypt($path,$pwd);
		$this->aes->decrypt($path,$pwd);
		$this->assertEquals($data,file_get_contents($path));
		$this->aes->encrypt($path,$pwd,FALSE);
		$this->aes->decrypt($path,$pwd,FALSE);
		$this->assertEquals($data,file_get_contents($path));
		$crypt = $this->aes->encrypt($data,$pwd);
		$this->assertEquals($data,$this->aes->decrypt($crypt,$pwd));
		$crypt = $this->aes->encrypt($data,$pwd,TRUE);
		$this->assertEquals($data,$this->aes->decrypt($crypt,$pwd,TRUE));
	}
	
	/**
	 *  Test encrypt() method
	 */
	public function testEncrypt() {
		$this->testDecrypt();
	}
	
	/**
	 *  Test fileDecrypt() method
	 */
	public function testFileDecrypt() {
		$path = LIB211_ROOT.'/module/AES/test.dat';
		$data = md5(time().microtime());
		$pwd = md5(time().microtime());
		file_put_contents($path,$data);
		$this->aes->fileEncrypt($path,$pwd);
		$this->aes->fileDecrypt($path,$pwd);
		$this->assertEquals($data,file_get_contents($path));
		$this->aes->fileEncrypt($path,$pwd,FALSE);
		$this->aes->fileDecrypt($path,$pwd,FALSE);
		$this->assertEquals($data,file_get_contents($path));
	}
	
	/**
	 *  Test fileEncrypt() method
	 */
	public function testFileEncrypt() {
		$this->testFileDecrypt();
	}
	
	/**
	 *  Test keySize() method
	 */
	public function testKeySize() {
		$this->aes->keySize(128);
		$this->testDecrypt();
		$this->testEncrypt();
		$this->testFileDecrypt();
		$this->testFileEncrypt();
		$this->testStrDecrypt();
		$this->testStrEncrypt();
		$this->aes->keySize(192);
		$this->testDecrypt();
		$this->testEncrypt();
		$this->testFileDecrypt();
		$this->testFileEncrypt();
		$this->testStrDecrypt();
		$this->testStrEncrypt();
		$this->aes->keySize(256);
		$this->testDecrypt();
		$this->testEncrypt();
		$this->testFileDecrypt();
		$this->testFileEncrypt();
		$this->testStrDecrypt();
		$this->testStrEncrypt();
	}
	
	/**
	 *  Test strDecrypt() method
	 */
	public function testStrDecrypt() {
		$data = md5(time().microtime());
		$pwd = md5(time().microtime());
		$crypt = $this->aes->strEncrypt($data,$pwd);
		$this->assertEquals($data,$this->aes->strDecrypt($crypt,$pwd));
		$crypt = $this->aes->strEncrypt($data,$pwd,TRUE);
		$this->assertEquals($data,$this->aes->strDecrypt($crypt,$pwd,TRUE));
	}
	
	/**
	 *  Test strEncrypt() method
	 */
	public function testStrEncrypt() {
		$this->testStrDecrypt();
	}
	
	
}
