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
 * LIB211 Database
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211Database extends LIB211Base {

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
	 * Constructor
	 */
	public function __construct($dsn) {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Database')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211DatabaseException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Database',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);
	}

	private function __clone() {}
	
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
	
	public static function init($dsn) {
		if (preg_match('/^([a-zA-Z0-9]+)\:/',$dsn,$m)===1) {
			$type = $m[1];
			$dsn = str_replace($type.':','',$dsn);
		} else throw new LIB211DatabaseException('Invalid DSN!');
		switch ($type) {
			case 'sqlite':
				require_once(LIB211_ROOT.'/module/Database/SQLite3.database.php');
				return new LIB211SQLite3Database($dsn);
			break;
			default:
				throw new LIB211DatabaseException('Unknown database type!');
			break;
		}
		return NULL;
	}
	
}

/**
 * LIB211 Database Interface
 * 
 * @author C!$C0^211
 * @package LIB211
 */
interface LIB211DatabaseInterface {

	public function __construct($dsn);
	
	public function addEntry($table,$data);

	public function addTable($name,$columns);

	public function check();

	public function deleteEntry($table,$expression);

	public function deleteTable($name);

	public function editEntry($table,$data,$expression);

	public function exec($query);

	public function getQueries();

	public function listTables();

	public function optimize();

	public function query($query);

	public function quote($string);

	public function readEntry($table,$columns,$expression=array(),$options=array());

}

/**
 * LIB211 Database Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211DatabaseException extends LIB211BaseException {
}