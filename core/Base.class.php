<?php

if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

class LIB211Base {

	private static $instances = 0;
	private static $time_diff = 0;
	private static $time_start = 0;
	private static $time_stop = 0;

	public function __check($check,$test) {
		
		switch ($check) {
			
			case 'class': case 'c':
				if (!class_exists($test,FALSE)) throw new LIB211BaseException('Missing class "'.$test.'".');
			break;
			
			case 'constant': case 'd':
				if (!defined($test)) throw new LIB211BaseException('Missing constant "'.$test.'".');
			break;
			
			case 'extension': case 'e':
				if (!extension_loaded($test)) throw new LIB211BaseException('Missing extension "'.$test.'".');
			break;
			
			case 'function': case 'f':
				if (!function_exists($test)) throw new LIB211BaseException('Missing function "'.$test.'".');
			break;
			
			case 'path': case 'p':
				if (!file_exists($test)) throw new LIB211BaseException('Missing path "'.$test.'".');
			break;
			
			case 'variable': case 'v':
				if (!isset($GLOBALS[$test])) throw new LIB211BaseException('Missing variable "$'.$test.'".');
			break;
			
		}
		
	}
	
	public function __construct() {
		self::$instances++;
		self::$time_start = microtime(TRUE);
	}
	
	public function __destruct() {
		self::$instances--;
	}
	
	public function __status() {
		self::$time_stop = microtime(TRUE);
		self::$time_diff = round(self::$time_stop - self::$time_start,11);
		$result = array();
		$result["instance"] = self::$instances;
		$result["runtime"] = self::$time_diff;
		return $result;
	}
	
}

interface LIB211BaseExceptionInterface {
    public function getMessage();
    public function getCode();
    public function getFile();
    public function getLine();
    public function getTrace();
    public function getTraceAsString();
    public function __toString();
    public function __construct($message = null, $code = 0);
}

class LIB211BaseException extends Exception implements LIB211BaseExceptionInterface {

    protected $message = 'Unknown exception';
    private $string;
    protected $code = 0;
    protected $file;
    protected $line;
    private $trace;
    
    public function __construct($message = null, $code = 0) {
        if (!$message) {
            throw new $this('Unknown '.get_class($this));
        }
        parent::__construct($message, $code);
    }
	
    public function __toString() {
        return get_class($this).' \''.$this->message.'\' in '.$this->file.':'.$this->line.EOL.$this->getTraceAsString();
    }
}