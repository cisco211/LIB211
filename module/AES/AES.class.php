<?php
/**
 * @package LIB211
 */

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
}

/**
 * LIB211 AES
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211AES extends LIB211Base {

	/**
	 * Instance counter
	 * @staticvar integer
	 */
	private static $instances = 0;
	
	/**
	 * Runtime of object
	 * @staticvar float
	 */
	private static $time_diff = 0;
	
	/**
	 * Start time of object
	 * @staticvar float
	 */
	private static $time_start = 0;
	
	/**
	 * Stop time of object
	 * @staticvar float
	 */
	private static $time_stop = 0;
	

	/**
	 * Encryption keysize
	 * @var integer
	 */
	private $keysize = 256;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211AES')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211AESException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211AES',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		parent::__destruct(); 
		self::$instances--;
	}

	/**
	 * Return object status
	 * @return array
	 */
	public function __status() {
		self::$time_stop = microtime(TRUE);
		self::$time_diff = round(self::$time_stop - self::$time_start,11);
		$result = array();
		$result['instance'] = self::$instances;
		$result['runtime'] = self::$time_diff;
		return $result;
	}
	
	/**
	 * Decrypt a string or file
	 * @param mixed $element
	 * @param string $pwd
	 * @param boolean $binary
	 * @return mixed
	 */
	public function decrypt($element,$pwd,$binary=NULL) {
		if (is_file($element)) {
			if ($binary === NULL) $binary = TRUE;
			return $this->fileDecrypt($element,$pwd,$binary);
		} else {
			if ($binary === NULL) $binary = FALSE;
			return $this->strDecrypt($element,$pwd,$binary);
		}
	}
	
	/**
	 * Encrypt a string or file
	 * @param mixed $element
	 * @param string $pwd
	 * @param boolean $binary
	 * @return mixed
	 */
	public function encrypt($element,$pwd,$binary=NULL) {
		if (is_file($element)) {
			if ($binary === NULL) $binary = TRUE;
			return $this->fileEncrypt($element,$pwd,$binary);
		} else {
			if ($binary === NULL) $binary = FALSE;
			return $this->strEncrypt($element,$pwd,$binary);
		}
	}
	
	/**
	 * Decrypt a file
	 * @param string $file
	 * @param string $pwd
	 * @param boolean $binary
	 * @return boolean
	 */
	public function fileDecrypt($file,$pwd,$binary=TRUE) {
		$ext = str_replace('.','',strrchr($file,'.'));
		if ($ext!='aes211') return FALSE;
		if (file_exists(str_replace('.aes211','',$file))) return FALSE;
		$handler = fopen($file,'r');
		$data = fread($handler,filesize($file));
		fclose($handler);
		$decrypt = $this->strDecrypt($data,$pwd,$binary);
		$handler = fopen(str_replace('.aes211','',$file),'wb');
		$data = fwrite($handler,$decrypt,strlen($decrypt));
		fclose($handler);
		return TRUE;
	}
	
	/**
	 * Encrypt a file
	 * @param string $file
	 * @param string $pwd
	 * @param boolean $binary
	 * @return boolean
	 */
	public function fileEncrypt($file,$pwd,$binary=TRUE) {
		if (!file_exists($file)) return FALSE;
		$handler = fopen($file,'rb');
		$data = fread($handler,filesize($file));
		fclose($handler);
		$crypt = $this->strEncrypt($data,$pwd,$binary);
		$handler = fopen($file.'.aes211','w');
		$data = fwrite($handler,$crypt,strlen($crypt));
		fclose($handler);
		return TRUE;
	}
	
	/**
	 * Set keysize
	 * @param integer $keysize
	 */
	public function keySize($keysize) {
		switch($keysize) {
			case 128: $this->keysize = 128; break;
			case 192: $this->keysize = 192; break;
			case 256: default: $this->keysize = 256; break;
		}
	}

	/**
	 * Decrypt a string
	 * @param string $crypt
	 * @param string $pwd
	 * @param boolean $binary
	 * @return string
	 */
	public function strDecrypt($crypt,$pwd,$binary=FALSE) {
		if (!$binary) {
			$explode = explode('x',$crypt);
			$string = pack('H*',$explode[1]);
			$iv = pack('H*',$explode[0]);
		}
		else {
			$size = strlen($crypt);
			$cut = strpos($crypt,'___AES211___');
			$iv = substr($crypt,0,$cut);
			$string = substr($crypt,($cut+12),($size-($cut+12)));
		}
		$handler = mcrypt_module_open('rijndael-'.$this->keysize,'','ofb','');
		$ks = mcrypt_enc_get_key_size($handler);
		$pwd = substr(md5($pwd),0,$ks);
		mcrypt_generic_init($handler,$pwd,$iv);
		$output = mdecrypt_generic($handler,$string);
		mcrypt_generic_deinit($handler);
		mcrypt_module_close($handler);
		return $output;
	}
	
	/**
	 * Encrypt a string
	 * @param string $string
	 * @param string $pwd
	 * @param boolean $binary
	 * @return string
	 */
	public function strEncrypt($string,$pwd,$binary=FALSE) {
		$handler = mcrypt_module_open('rijndael-'.$this->keysize,'','ofb','');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($handler),MCRYPT_RAND);
		$ks = mcrypt_enc_get_key_size($handler);
		$pwd = substr(md5($pwd),0,$ks);
		mcrypt_generic_init($handler,$pwd,$iv);
		$enc = mcrypt_generic($handler,$string);
		if (!$binary) $output = bin2hex($iv).'x'.bin2hex($enc);
		else $output = $iv.'___AES211___'.$enc;
		mcrypt_generic_deinit($handler);
		mcrypt_module_close($handler);
		return $output;
	}
	
}

/**
 * LIB211 Example Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211AESException extends LIB211BaseException {
}