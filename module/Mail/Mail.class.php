<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
}

/**
 * LIB211 Mail
 * 
 * @author C!$C0^211
 *
 */
class LIB211Mail extends LIB211Base {

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
	 * Additional header data for mail
	 * @var string
	 */
	private $addheader = '';
	
	/**
	 * Blind carboncopy header for mail
	 * @var string
	 */
	private $bcc = '';
	
	/**
	 * Carboncopy header for mail
	 * @var string
	 */
	private $cc = '';
	
	/**
	 * File(s) for mail
	 * @var array
	 */
	private $file = array();
	
	/**
	 * Filecount for mail
	 * @var integer
	 */
	private $filecount = 0;
	
	/**
	 * From header for mail
	 * @var string
	 */
	private $from = '';
	
	/**
	 * Header data for mail
	 * @var string
	 */
	private $header = '';
	
	/**
	 * Full mail data
	 * @var unknown_type
	 */
	private $maildata = '';
	
	/**
	 * Message for mail
	 * @var string
	 */
	private $message = '';
	
	/**
	 * Reply to header for mail
	 * @var string
	 */
	private $reply = '';
	
	/**
	 * Subject header for mail
	 * @var string
	 */
	private $subject = '';
	
	/**
	 * To header for mail
	 * @var string
	 */
	private $to = '';
	
	/**
	 * MIME-Type for mail
	 * @var string
	 */
	private $type = '';
	
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/lib211.lock')) {
			$this->__check('d','PHP_OS');
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211MailException');
			touch(LIB211_ROOT.'/lib211.lock',time());
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
		$result['bcc'] = $this->bcc;
  		$result['cc'] = $this->cc;
		$result['file'] = $this->file;
		$result['filecount'] = $this->filecount;
		$result['from'] = $this->from;
		$result['message'] = $this->message;
		$result['reply'] = $this->reply;
		$result['subject'] = $this->subject;
		$result['to'] = $this->to;
		$result['type'] = $this->type;
		$result['header'] = $this->header;
		$result['addheader'] = $this->addheader;
		$result['maildata'] = $this->maildata;
		return $result;
	}
	
	/**
	 * Set attachment
	 * @param string $file
	 * @throws LIB211MailException
	 * @return boolean
	 */
	public function attachment($file) {
		if (!file_exists($file)) throw new LIB211MailException('File "'.$file.'" does not exist');
		if (!is_file($file)) throw new LIB211MailException('Unknown file type for "'.$file.'"');
		if (!is_readable($file)) throw new LIB211MailException('File "'.$file.'" is not readable');
		if (!strrchr($file,'/')) $filename = $file;
		else $filename = str_replace('/','',strrchr($file,'/'));
		$this->filecount++;
		$this->file[$this->filecount]['name'] = $filename;
		$this->file[$this->filecount]['data'] = chunk_split(base64_encode(file_get_contents($file)));
		return TRUE;
	}
	
	/**
	 * Set blind carboncopy address
	 * @param string $address
	 * @throws LIB211MailException
	 * @return boolean
	 */
	public function blindcopy($address) {
		if (strpos($address,'@') > 3) $this->bcc = trim($address);
		else throw new LIB211MailException('Invalid email address "'.$address.'"');
		return TRUE;
	}
	
	/**
	 * Set carboncopy address
	 * @param string $address
	 * @throws LIB211MailException
	 * @return boolean
	 */
	public function carboncopy($address) {
		if (strpos($address,'@') > 3) $this->cc = trim($address);
		else throw new LIB211MailException('Invalid email address "'.$address.'"');
		return TRUE;
	}
	
	/**
	 * Flush (delete) email
	 * @return boolean
	 */
	public function flush() {
		$this->addheader = '';
		$this->bcc = '';
		$this->cc = '';
		$this->file = array();
		$this->filecount = 0;
		$this->from = '';
		$this->header = '';
		$this->maildata = '';
		$this->message = '';
		$this->reply = '';
		$this->subject = '';
		$this->to = '';
		$this->type = '';
		return TRUE;
	}
	
	/**
	 * Set sender address
	 * @param string $address
	 * @throws LIB211MailException
	 * @return boolean
	 */
	public function from($address) {
		if (strpos($address,'@') > 3) $this->from = trim($address);
		else throw new LIB211MailException('Invalid email address "'.$address.'"');
		return TRUE;
	}
	
	/**
	 * Set header
	 * @param string $header
	 * @param string $content
	 * @return boolean
	 */
	public function header($header,$content) {
		$this->addheader .= $header.': '.$content."\r\n";
		return TRUE;
	}

	/**
	 * Set email message
	 * @param string $message
	 * @param string $type
	 * @return boolean
	 */
	public function message($message,$type) {
		$message = wordwrap($message,70);
		if (PHP_OS === 'WINNT') $message = str_replace("\n.","\n..",$message);
		$this->message = $message;
		$this->type = $type;
		return TRUE;
	}
	
	/**
	 * Set reply-to address
	 * @param string $address
	 * @throws LIB211MailException
	 * @return boolean
	 */
	public function reply($address) {
		if (strpos($address,'@') > 3) $this->reply = trim($address);
		else throw new LIB211MailException('Invalid email address "'.$address.'"');
		return TRUE;
	}
	
	/**
	 * Send email
	 * @param boolean $return
	 * @throws LIB211MailException
	 * @return string|boolean
	 */
	public function send($return = FALSE) {
		$boundary = strtoupper(md5(uniqid(time())));
		$this->header .= 'From: '.$this->from."\r\n";
		if ($this->reply !== '') $this->header .= 'Reply-To: '.$this->reply."\r\n";
		$this->header .= 'X-Mailer: PHP/'.phpversion()."/LIB211Mail\r\n";
		if ($this->addheader !== '') $this->header .= $this->addheader;
		if ($this->bcc !== '') $this->header .= 'Bcc: '.$this->bcc."\r\n";
		if ($this->cc !== '') $this->header .= 'Cc: '.$this->cc."\r\n";
		$this->header .= "MIME-Version: 1.0\r\n";
		$this->header .= 'Content-Type: multipart/mixed; boundary='.$boundary."\r\n";
		foreach($this->file as $file) {
			$this->header .= '--'.$boundary."\r\n";
			$this->header .= 'Content-Type: application/octetstream; name="'.$file["name"]."\"\r\n";
			$this->header .= "Content-Transfer-Encoding: base64\r\n";
			$this->header .= 'Content-Disposition: attachment; filename="'.$file["name"]."\"\r\n\r\n";
			$this->header .= $file["data"]."\r\n";
		}
		$message = '--'.$boundary."\r\n";
		switch($this->type){
			case 'text/html':
				$message .= "Content-Type: text/html; charset=UTF-8\r\n";
				$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
			break;
			case 'text/plain':
				$message .= "Content-Type: text/plain;\r\n";
				$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
			break;
			default:
				$message .= "Content-Type: text/plain;\r\n";
				$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
			break;
		}
		$message .= $this->message."\r\n";
		$message .= '--'.$boundary."--\r\n\r\n";
		$this->maildata = $this->header.$message;
		if ($return === TRUE) return $this->maildata;
		elseif(mail($this->to,$this->subject,$message,$this->header)) return TRUE;
		else throw new LIB211MailException('Email could not be sent');
	}
	
	/**
	 * Set email subject
	 * @param string $subject
	 * @return boolean
	 */
	public function subject($subject) {
		$this->subject = $subject;
		return TRUE;
	}
	
	/**
	 * Set receiver address
	 * @param string $address
	 * @throws LIB211MailException
	 * @return boolean
	 */
	public function to($address) {
		if (strpos($address,"@") > 3) $this->to = trim($address);
		else throw new LIB211MailException('Invalid email address "'.$address.'"');
		return TRUE;
	}
					
}

/**
 * LIB211 Mail Exception
 * 
 * @author C!$C0^211
 *
 */
class LIB211MailException extends LIB211BaseException {
}