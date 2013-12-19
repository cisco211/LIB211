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
 * LIB211 Crontab
 * 
 * An exact command can exist only once (like a primary key).
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211Crontab extends LIB211Base {
	
	/**
	 * Stores current crontab data
	 */
	private $data = array();
	
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
	 * Stores current user
	 * 
	 */
	private $user = NULL;
	
	/**
	 * Add an entry by an given array
	 * @return boolean
	 */
	private function _entryAdd($array) {

		// Input variable
		$input = $this->_entryDefault();
		
		// Comment
		if ($array['comment'] !== NULL) {
			$input['comment'] = $array['comment'];
			$this->data[] = $input;
			
		// Command
		} else {
			
			// Check existance
			if ($this->_entryExists($array) != -1) return FALSE;
			
			// Check command existance
			$found = $this->_entryFind(array('command'=>$array['command']));
			if (!empty($found)) return FALSE;
			
			$input = $array;
			$input['comment'] = NULL;
			$this->data[] = $input;
		}
		
		// Return success
		return TRUE;
	}
	
	/**
	 * Return default entry
	 * @return array
	 */
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
	 * Delete entry by given index
	 * @return boolean
	 */
	private function _entryDelete($index) {
		if (isset($this->data[$index])) {
			unset($this->data[$index]);
			return TRUE;
		} else return FALSE;
	}
	
	/**
	 * Edit an entry by an given array
	 * @return boolean
	 */
	private function _entryEdit($index,$array) {
		
		// Input variable
		$input = $this->_entryDefault();
		
		// Check for existance
		if (!isset($this->data[$index])) return FALSE;
		
		// Comment
		if ($array['comment'] !== NULL) {
			$input['comment'] = $array['comment'];
			$this->data[$index] = $input;
			
		// Command
		} else {
			$input = $array;
			$input['comment'] = NULL;
			$this->data[$index] = $input;
		}
		
		// Return success
		return TRUE;
	}
	
	/**
	 * Check if given exact entry exists or not
	 * @return integer
	 */
	private function _entryExists($array) {
		foreach ($this->data as $index => $entry) {
			if ($entry == $array) return $index;
		}
		return -1;
	}
	
	/**
	 * Find entries by a given filter
	 * $filter = array(
	 * 	'minute' = Filter by minute
	 * 	'hour' = Filter by hour 
	 * 	'monthday' = Filter by day of month
	 * 	'month' = Filter by month
	 * 	'weekday' = Filter by day of week
	 * 	'command' = Filter by command
	 * 	'commandSW' = Filter by command starts with
	 * 	'commandEW' = Filter by command ends with
	 * 	'commandEX' = Filter by command expression
	 *  'comment' = Filter by comment
	 * 	'commentSW' = Filter by comment starts with
	 * 	'commentEW' = Filter by comment ends with
	 * 	'commentEX' = Filter by comment expression
	 * );
	 * @return array
	 */
	private function _entryFind($filter=array()) {
		
		// Output array
		$output = array();
		
		// Find by given index
		if (is_integer($filter) AND isset($this->data[$filter])) {
			return array($filter=>$this->data[$filter]);
		
		// Find by given filter
		} else {
			
			// Iterate over data
			foreach ($this->data as $index => $entry) {
				
				// Find minute
				$foundMinute = FALSE;
				if (!isset($filter['minute']) OR $filter['minute'] == $entry['minute']) $foundMinute = TRUE;
				
				// Find hour
				$foundHour = FALSE;
				if (!isset($filter['hour']) OR $filter['hour'] == $entry['hour']) $foundHour = TRUE;
				
				// Find month of day
				$foundMonthDay = FALSE;
				if (!isset($filter['monthday']) OR $filter['monthday'] == $entry['monthday']) $foundMonthDay = TRUE;
				
				// Find month
				$foundMonth = FALSE;
				if (!isset($filter['month']) OR $filter['month'] == $entry['month']) $foundMonth = TRUE;
				
				// Find day of week
				$foundWeekDay = FALSE;
				if (!isset($filter['weekday']) OR $filter['weekday'] == $entry['weekday']) $foundWeekDay = TRUE;
				
				// Find command
				$foundCommand = FALSE;
				if (!isset($filter['command']) OR $filter['command'] == $entry['command']) $foundCommand = TRUE;
				
				// Find command starts with
				$foundCommandStart = FALSE;
				if (!isset($filter['commandSW']) OR $filter['commandSW'] == substr($entry['command'],0,strlen($filter['commandSW']))) $foundCommandStart = TRUE;
				
				// Find command ends with
				$foundCommandEnd = FALSE;
				if (!isset($filter['commandEW']) OR $filter['commandEW'] == substr($entry['command'],(strlen($filter['commandEW'])*-1))) $foundCommandEnd = TRUE;
				
				// Find command expression
				$foundCommandExpression = FALSE;
				if (!isset($filter['commandEX']) OR @preg_match($filter['commandEX'],$entry['command']) == 1) $foundCommandExpression = TRUE;
				
				// Find comment
				$foundComment = FALSE;
				if (!isset($filter['comment']) OR $filter['comment'] == $entry['comment']) $foundComment = TRUE;
				
				// Find comment starts with
				$foundCommentStart = FALSE;
				if (!isset($filter['commentSW']) OR $filter['commentSW'] == substr($entry['comment'],0,strlen($filter['commentSW']))) $foundCommentStart = TRUE;
				
				// Find comment ends with
				$foundCommentEnd = FALSE;
				if (!isset($filter['commentEW']) OR $filter['commentEW'] == substr($entry['comment'],(strlen($filter['commentEW'])*-1))) $foundCommentEnd = TRUE;
				
				// Find comment expression
				$foundCommentExpression = FALSE;
				if (!isset($filter['commentEX']) OR @preg_match($filter['commentEX'],$entry['comment']) == 1) $foundCommentExpression = TRUE;
				
				// Add to output
				if ($foundMinute AND
					$foundHour AND
					$foundMonthDay AND
					$foundMonth AND
					$foundWeekDay AND
					$foundCommand AND
					$foundCommandStart AND
					$foundCommandEnd AND
					$foundCommandExpression AND
					$foundComment AND
					$foundCommentStart AND
					$foundCommentEnd AND
					$foundCommentExpression)
						$output[$index] = $entry;
			}
		}
		
		// Return output
		return $output;
	}
	
	/**
	 * Parse crontab data
	 * @return array
	 */
	private function _parse($rows) {
		
		// Empty data
		$this->data = array();
		
		// Check rows
		if (!is_array($rows) OR empty($rows)) return $this->data;
		
		// Process rows
		foreach ($rows as $row) $this->data[] = $this->_parseLine($row);
		
		return $this->data;
	}
	
	/**
	 * Parse crontab line
	 * @return array
	 */
	private function _parseLine($line) {
		
		// Output variable
		$output = $this->_entryDefault();
		
		// Comment
		if (strpos($line,'#') === 0) $output['comment'] = trim(substr($line,1));
		
		// Command
		else if (preg_match('/^([0-9\*\/\,\-]+) ([0-9\*\/\,\-]+) ([0-9\*\/\,\-\?LW]+) ([0-9\*\/\,\-]+) ([0-9\*\/\,\-\?L\#]+) (.+)$/',trim($line),$m) == 1) {
			$output['minute'] = $m[1];
			$output['hour'] = $m[2];
			$output['monthday'] = $m[3];
			$output['month'] = $m[4];
			$output['weekday'] = $m[5];
			$output['command'] = $m[6];
		}
		
		// Return output
		return $output;
	}
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Crontab')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211CrontabException');
			$this->__check('f','shell_exec');
			$this->__check('f','trim');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Crontab',time());
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
	 * Add entry by given array
	 */
	public function add($array) {
		return $this->_entryAdd($array);
	}
	
	/**
	 * Add command entry
	 * @return boolean
	 */
	public function addCommand($minute,$hour,$monthday,$month,$weekday,$command) {
		$input = $this->_entryDefault();
		$input['minute'] = $minute;
		$input['hour'] = $hour;
		$input['monthday'] = $monthday;
		$input['month'] = $month;
		$input['weekday'] = $weekday;
		$input['command'] = $command;
		return $this->_entryAdd($input);
	}
	
	/**
	 * Add comment entry
	 * @return boolean
	 */
	public function addComment($comment) {
		$input = $this->_entryDefault();
		$input['comment'] = $comment;
		return $this->_entryAdd($input);
	}
	
	/**
	 * Add entry by given line
	 * @return boolean
	 */
	public function addLine($line) {
		return $this->_entryAdd($this->_parseLine($line));
	}
	
	/**
	 * Delete entry by given index
	 * @return boolean
	 */
	public function delete($index) {
		return $this->_entryDelete($index);
	}
	
	/**
	 * Delete command entry
	 * @return boolean
	 */
	public function deleteCommand($command) {
		$input = $this->_entryDefault();
		$input['command'] = $command;
		$entries = $this->_entryFind($input);
		$status = FALSE;
		foreach ($entries as $index => $entry) {
			if ($entry['command'] == $input['command']) {
				if ($this->_entryDelete($index)) $status = TRUE;
				else $status = FALSE;
			}
		}
		return $status;
	}

	/**
	 * Edit entry by command from given line
	 */
	public function deleteCommandLine($line) {
		$input = $this->_parseLine($line);
		if ($input['command'] === NULL) return FALSE;
		$entries = $this->_entryFind(array('command'=>$input['command']));
		$status = FALSE;
		foreach ($entries as $index => $entry) {
			if ($entry['command'] == $input['command']) {
				if ($this->_entryDelete($index,$input)) $status = TRUE;
				else $status = FALSE;
			}
		}
		return $status;
	}
	
	/**
	 * Delete comment entry
	 * @return boolean
	 */
	public function deleteComment($comment) {
		$input = $this->_entryDefault();
		$input['comment'] = $comment;
		$entries = $this->_entryFind($input);
		$status = FALSE;
		foreach ($entries as $index => $entry) {
			if ($entry['comment'] == $input['comment']) {
				if ($this->_entryDelete($index)) $status = TRUE;
				else $status = FALSE;
			}
		}
		return $status;
	}
	
	/**
	 * Delete entry by given line
	 * @return boolean
	 */
	public function deleteLine($line) {
		$input = $this->_parseLine($line);
		$entries = $this->_entryFind($input);
		$status = FALSE;
		foreach ($entries as $index => $entry) {
			if ($this->_entryExists($input) == $index) {
				if ($this->_entryDelete($index)) $status = TRUE;
				else $status = FALSE;
			}
		}
		return $status;
	}
	
	/**
	 * Edit entry by given id and array
	 * @return boolean
	 */
	public function edit($index,$array) {
		return $this->_entryEdit($index,$array);
	}
	
	/**
	 * Edit entry by command from given data
	 * @return boolean
	 */
	public function editCommand($minute,$hour,$monthday,$month,$weekday,$command) {
		$input = $this->_entryDefault();
		$input['minute'] = $minute;
		$input['hour'] = $hour;
		$input['monthday'] = $monthday;
		$input['month'] = $month;
		$input['weekday'] = $weekday;
		$input['command'] = $command;
		$entries = $this->_entryFind(array('command'=>$command));
		$status = FALSE;
		foreach ($entries as $index => $entry) {
			if ($entry['command'] == $input['command']) {
				if ($this->_entryEdit($index,$input)) $status = TRUE;
				else $status = FALSE;
			}
		}
		return $status;
	}
	
	/**
	 * Edit entry by command from given line
	 */
	public function editCommandLine($line) {
		$input = $this->_parseLine($line);
		if ($input['command'] === NULL) return FALSE;
		$entries = $this->_entryFind(array('command'=>$input['command']));
		$status = FALSE;
		foreach ($entries as $index => $entry) {
			if ($entry['command'] == $input['command']) {
				if ($this->_entryEdit($index,$input)) $status = TRUE;
				else $status = FALSE;
			}
		}
		return $status;
	}
	
	/**
	 * Edit entry by index from comment
	 * @return boolean
	 */
	public function editComment($index,$comment) {
		$input = $this->_entryDefault();
		$input['comment'] = $comment;
		return $this->_entryEdit($index,$input);
	}
	
	/**
	 * Edit entry by index from line
	 * @return boolean
	 */
	public function editLine($index,$line) {
		$input = $this->_parseLine($line);
		return $this->_entryEdit($index,$input);
	}
	
	/**
	 * Find entry by given filter
	 * @return array
	 */
	public function find($filter=array()) {
		return $this->_entryFind($filter);
	}
	
	/**
	 * Get all entries
	 * @return array
	 */
	public function findAll() {
		return $this->data;
	}
	
	/**
	 * Find entry by given command
	 * @return array
	 */
	public function findCommand($command) {
		return $this->_entryFind(array('command'=>$command));
	}
	
	/**
	 * Find entry by given comment
	 * @return array
	 */
	public function findComment($comment) {
		return $this->_entryFind(array('comment'=>$comment));
	}
	
	/**
	 * Find entry by given line
	 * @return array
	 */
	public function findLine($line) {
		return $this->_entryFind($this->_parseLine($line));
	}
	
	/**
	 * Read crontab file
	 * @return array
	 */
	public function read() {
		
		// Execute command
		$rows = array();
		exec('crontab -u '.$this->user.' -l',$rows);
		
		// Parse and return data
		return $this->_parse($rows);
	}
	
	/**
	 * Empty data
	 * @return boolean
	 */
	public function reset() {
		$this->data = array();
		return TRUE;
	}
	
	/**
	 * Get/Set current user
	 * @return string
	 */
	public function user($name=NULL) {
		
		// Get current user
		if ($this->user === NULL) $this->user = trim(shell_exec('whoami'));
		
		// Set current user
		if ($name !== NULL) $this->user = $name;
		
		// Return current user
		return $this->user;
	}
	
	/**
	 * Write crontab file
	 * @return boolean
	 */
	public function write() {
		
		// Create temp file
		$file = tempnam(sys_get_temp_dir(),'PHP_CRONTAB_');
		if ($file === FALSE) return FALSE;
		
		// Open temp file
		$handler = @fopen($file,'w');
		if ($handler === FALSE) return FALSE;
		
		// Iterate over data
		foreach ($this->data as $index => $entry) {
			if ($entry['comment'] != NULL) fwrite($handler,'# '.$entry['comment'].PHP_EOL);
			else fwrite($handler,$entry['minute'].' '.$entry['hour'].' '.$entry['monthday'].' '.$entry['month'].' '.$entry['weekday'].' '.$entry['command'].PHP_EOL);
		}
		
		// Close temp file
		if (!fclose($handler)) return FALSE;
		
		// Add crontab
		exec('crontab -u '.$this->user.' '.$file);
		
		// Delete temp file
		@unlink($file); 
		
		// Return success
		return TRUE;
	}
}

/**
 * LIB211 Crontab Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211CrontabException extends LIB211BaseException {
}