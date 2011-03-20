<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
}

/**
 * LIB211 Base class
 * 
 * @author C!$C0^211
 *
 */
class LIB211Base {

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
	 * Check for availability of a class/constant/extension/function/path/variable ^^
	 * @param string $check
	 * @param string $test
	 * @throws LIB211BaseException
	 */
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
	
	/**
	 * Constructor
	 */
	public function __construct() {
		self::$instances++;
		self::$time_start = microtime(TRUE);
	}
	
	/**
	 * Destructor
	 */
	public function __destruct() {
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
		$result["instance"] = self::$instances;
		$result["runtime"] = self::$time_diff;
		return $result;
	}
	
}

/**
 * LIB211 Base Exception Interface
 * 
 * @author C!$C0^211
 *
 */
interface LIB211BaseExceptionInterface {
	
    /**
     * Get the error message
     */
    public function getMessage();
    
    /**
     * Get the error code
     */
    public function getCode();

    /**
     * Get the error file
     */
    public function getFile();
    
    /**
     * Get the error line
     */
    public function getLine();
    
    /**
     * Get the stacktrace
     */
    public function getTrace();
    
    /**
     * Get the stacktrace as string
     */
    public function getTraceAsString();
    
    /**
     * Convert exception to string
     */
    public function __toDefault();
    
    /**
     * Convert exception to string with stacktrace
     */
    public function __toString();
    
    /**
     * Constructor
     * @param string $message
     * @param integer $code
     */
    public function __construct($message = NULL, $code = 0);
}

/**
 * LIB211 Base Exception
 * 
 * @author C!$C0^211
 *
 */
class LIB211BaseException extends Exception implements LIB211BaseExceptionInterface {

    /**
     * Exception message
     * @var string
     */
    protected $message = 'Unknown exception';
    
    /**
     * Exception string
     * @var string
     */
    private $string;
    
    /**
     * Exception code
     * @var integer
     */
    protected $code = 0;
    
    /**
     * Exception file
     * @var string
     */
    protected $file;
    
    /**
     * Exception line
     * @var integer
     */
    protected $line;
    
    /**
     * Exception stacktrace
     * @var string
     */
    private $trace;
    
    /**
     * Constructor
     * @param string $message
     * @param integer $code
     */
    public function __construct($message = NULL, $code = 0) {
        if (!$message) {
            throw new $this('Unknown '.get_class($this));
        }
        parent::__construct($message, $code);
    }
	
    /**
     * Convert exception to string
     * @return string
     */
    public function __toDefault() {
    	return get_class($this).': '.$this->message.' in '.$this->file.':'.$this->line;
    }
    
    /**
     * Convert exception to string with stacktrace
     * @return string
     */
    public function __toString() {
        return get_class($this).': '.$this->message.' in '.$this->file.':'.$this->line.EOL.$this->getTraceAsString();
    }
    
}