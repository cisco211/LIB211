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
 * LIB211 HTTPd_htconf
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPd_htconf extends LIB211Base {

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
	
	private $patternComment = '/^\s*\\#\s*(.*)$/';
	private $patternParameter = '/^\s*(\w+)\s+(.*)$/';
	private $patternGroupStart = '/^\s*<(\w+)\s+(.*)>\s*$/';
	private $patternGroupEnd = '/^\s*<\/(\w+)>$/';
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211HTTPd_htconf')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211HTTPd_htconfException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211HTTPd_htconf',time());
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
		$test = array(
			0=>array('text'=>'Example comment'),
			1=>array('key'=>'rootKey','value'=>'rootValue'),
			2=>array('sub'=>'IfDefine','expr'=>'mod_core.c','data'=>array(
				0=>array('key'=>'subKey','value'=>'subValue'),
			)),
		);
		
		===
		
		# Example comment
		rootKey rootValue
		<IfDefine mod_core.c>
			subKey subValue
		</IfDefine>
		
	 */
	
	/**
	 * Returns apache config lines of array
	 */
	private function _array2lines($array) {
		$l = '';
		$c = count($array);
		for ($i = 0; $i < $c; $i++) {
			
			// Skip invalid entry
			if (!is_array($array[$i])) continue;
			
			// Comment
			if (isset($array[$i]['text'])) {
				$l .= '# '.$array[$i]['text'].EOL;
				continue;
			}
			
			// Parameter
			if (isset($array[$i]['key'],$array[$i]['value'])) {
				$l .= $array[$i]['key'].' '.$array[$i]['value'].EOL;
				continue;
			}
			
			// Group
			if (isset($array[$i]['sub'],$array[$i]['expr'],$array[$i]['data'])) {
				$t = 0;
				$l .= $this->_array2linesGroup($t,$array[$i]['sub'],$array[$i]['expr'],$array[$i]['data']);
			}
			
		}
		return $l;
	}
	
	/**
	 * Returns recursive apache config lines of array
	 */
	private function _array2linesGroup(&$t,$sub,$expr,$array) {
		$s = '';
		$s .= $this->_tab($t).'<'.$sub.' '.$expr.'>'.EOL;
		$t++;
		$c = count($array);
		for ($i = 0; $i < $c; $i++) {
			// Skip invalid entry
			if (!is_array($array[$i])) continue;
			
			// Comment
			if (isset($array[$i]['text'])) {
				$s .= $this->_tab($t).'# '.$array[$i]['text'].EOL;
				continue;
			}
			
			// Parameter
			if (isset($array[$i]['key'],$array[$i]['value'])) {
				$s .= $this->_tab($t).$array[$i]['key'].' '.$array[$i]['value'].EOL;
				continue;
			}
			
			// Group
			if (isset($array[$i]['sub'],$array[$i]['expr'],$array[$i]['data'])) {
				$s .= $this->_array2linesGroup($t,$array[$i]['sub'],$array[$i]['expr'],$array[$i]['data']);
			}
			
		}
		$t--;
		$s .= $this->_tab($t).'</'.$sub.'>'.EOL;
		return $s;
	}
	
	/**
	 * Returns array of apache config lines
	 */
	private function _lines2array($lines) {
		
		// Output array
		$a = array();
		
		// Line count
		$c = count($lines);
		
		// Current line
		$l = 0;
		
		$i = -1;
		
		// Line walker
		for ($l; $l < $c; $l++) {
			
			// Comment
			if (preg_match($this->patternComment,$lines[$l],$m) === 1) {
				$i++;
				$a[$i] = array('text'=>trim($m[1]));
				continue;
			}
			
			// Parameter
			if (preg_match($this->patternParameter,$lines[$l],$m) === 1) {
				$i++;
				$a[$i] = array('key'=>trim($m[1]),'value'=>trim($m[2]));
				continue;
			}
			
			// Group start
			if (preg_match($this->patternGroupStart,$lines[$l],$m) === 1) {
				$i++;
				$s = $i;
				$a[$i] = $this->_lines2arrayGroup($lines,$l,$c,$m);
				continue;
			}
			
			// Group end
			if (preg_match($this->patternGroupEnd,$lines[$l],$m) === 1) {
				return FALSE;
			}
		}
		
		// Return result
		return $a;
	}
	
	/**
	 * Returns recursive array of apache config lines
	 */
	private function _lines2arrayGroup(&$lines,&$l,$c,$m) {
		
		// Name of group
		$sub = $m[1];
		
		// Starting array
		$s = $l;
		
		// Array head
		$a = array('sub'=>$m[1],'expr'=>$m[2],'data'=>array());
		
		// Next line
		$l++;
		
		// Current inner position
		$i = -1;
		
		// Line walker
		for ($l; $l < $c; $l++) {
			
			// Comment
			if (preg_match($this->patternComment,$lines[$l],$m)) {
				$i++;
				$a['data'][$i] = array('text'=>trim($m[1]));
				continue;
			}
			
			// Parameter
			if (preg_match($this->patternParameter,$lines[$l],$m) === 1) {
				$i++;
				$a['data'][$i] = array('key'=>trim($m[1]),'value'=>trim($m[2]));
				continue;
			}
			
			// Group start
			if (preg_match($this->patternGroupStart,$lines[$l],$m) === 1) {
				$i++;
				$a['data'][$i] = $this->_lines2arrayGroup($lines,$l,$c,$m);
				continue;
			}
			
			// Group end
			if (preg_match($this->patternGroupEnd,$lines[$l],$m) === 1) {
				
				// End of group found
				if ($sub == $m[1]) {
					$l++;
					break;
				}
				
				// Invalid group end
				return FALSE;
			}
		}
		
		// Return result
		return $a;
	}
	
	/**
	 * Read conf
	 */
	public function read($file) {
		if (file_exists($file)) {
			$lines = file($file);
			if ($lines === FALSE) return array();
			return $this->_lines2array($lines);
		} else {
			$lines = explode('\n',$file);
			if (!is_array($lines)) return array();
			return $this->_lines2array($lines);
		}
	}
	
	/**
	 * Tab indent
	 */
	private function _tab($t) {
		$tab = '';
		for ($i = 0; $i < $t; $i++) $tab .= chr(9);
		return $tab;
	}
	
	/**
	 * Write conf
	 */
	public function write($file,$array) {
		$data = $this->_array2lines($array);
		@file_put_contents($file,$data);
		return $data;
	}
}

/**
 * LIB211 HTTPd_htconf Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211HTTPd_htconfException extends LIB211BaseException {
}