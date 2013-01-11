<?php
/**
 * @package LIB211
 */

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
}

class LIB211SQLite3Database implements LIB211DatabaseInterface {

	private $db = NULL;

	private $queries = 0;

	private $queryList = array();

	private $tables = array();

	public function __construct($dsn) {
		try {
			$this->db = new PDO('sqlite:'.$dsn);
		} catch (PDOException $e) {
			throw new LIB211DatabaseException($e);
		}
		try {
			$this->query(file_get_contents(LIB211_ROOT.'/module/Database/SQLite3.pragma.sql'));
		}catch (PDOException $e) {
			throw new LIB211DatabaseException($e);
		}
		$tmp = $this->readEntry('sqlite_master','name',array('column'=>'type','operator'=>'=','value'=>'table'),array('sortColumn'=>'name'));
		foreach($tmp as $table) $this->tables[] = $table['name'];
		unset($tmp);
	}

	public function __destruct() {
		$this->handler = NULL;
	}

	public function addEntry($table,$data) {
		$sql = 'INSERT INTO \''.$table.'\' (';
		$columns = array();
		$values = array();
		foreach($data as $column => $value) {
			$columns[] = $column;
			$values[] = $value;
		}
		$i = 0;
		$count = count($columns)-1;
		foreach($columns as $column) {
			$sql .= '\''.$column.'\'';
			if($i != $count) $sql .= ',';
			$i++;
		}
		$sql .= ') VALUES (';
		$i = 0;
		$count = count($values)-1;
		foreach($values as $column) {
			$sql .= $this->quote($column);
			if($i != $count) $sql .= ',';
			$i++;
		}
		$sql .= ');';
		return $this->exec($sql);
	}

	public function addTable($name,$columns) {
		$sql = 'CREATE TABLE \''.$name.'\' (';
		$i = 0;
		$count = count($columns)-1;
		foreach($columns as $column => $properties) {
			$sql .= $column;
			if (isset($properties['type'])) $sql .= ' '.$properties['type'];
			if (isset($properties['primaryKey']) AND $properties['primaryKey'] === TRUE) $sql .= ' PRIMARY KEY';
			if (isset($properties['notNull']) AND $properties['notNull'] === TRUE) $sql .= ' NOT NULL';
			if($i != $count) $sql .= ',';
			$i++;
		}
		$sql .= ');';
		return $this->exec($sql);
	}

	public function check() {
		$result = $this->query('PRAGMA integrity_check;');
		$output = array();
		foreach($result as $row) $output[] = $row;
		return $output;
	}

	public function deleteEntry($table,$expression) {
		$sql = 'DELETE FROM \''.$table.'\' WHERE';
		if (isset($expression['column'])) $sql .= ' '.$expression['column'];
		if (isset($expression['operator'])) $sql .= ' '.$expression['operator'];
		if (isset($expression['value'])) $sql .= ' '.$this->quote($expression['value']);
		if (isset($expression['and'])) {
			$sql .= ' AND ';
			if (isset($expression['and']['column'])) $sql .= ' '.$expression['and']['column'];
			if (isset($expression['and']['operator'])) $sql .= ' '.$expression['and']['operator'];
			if (isset($expression['and']['value'])) $sql .= ' '.$this->quote($expression['and']['value']);
		}
		$sql .= ';';
		$result = $this->exec($sql);
		$this->optimize();
		return $result;
	}

	public function deleteTable($name) {
		$result = $this->exec('DROP TABLE \''.$name.'\';');
		$this->optimize();
		return $result;
	}

	public function editEntry($table,$data,$expression) {
		$sql = 'UPDATE \''.$table.'\' SET';
		$i = 0;
		$count = count($data)-1;
		foreach($data as $column => $value) {
			$sql .= ' \''.$column.'\'='.$this->quote($value);
			if($i != $count) $sql .= ',';
			$i++;
		}
		$sql .= ' WHERE ';
		if (isset($expression['column'])) $sql .= ' '.$expression['column'];
		if (isset($expression['operator'])) $sql .= ' '.$expression['operator'];
		if (isset($expression['value'])) $sql .= ' '.$this->quote($expression['value']);
		if (isset($expression['and'])) {
			$sql .= ' AND ';
			if (isset($expression['and']['column'])) $sql .= ' '.$expression['and']['column'];
			if (isset($expression['and']['operator'])) $sql .= ' '.$expression['and']['operator'];
			if (isset($expression['and']['value'])) $sql .= ' '.$this->quote($expression['and']['value']);
		}
		$sql .= ';';
		return $this->exec($sql);
	}

	public function exec($query) {
		try {
			$this->queries++;
			$this->queryList[] = $query;
			$result = $this->db->exec($query);
			return $result;
		} catch (PDOException $e) {
			FW_Exception::showError($e);
		}
	}

	public function getQueries() {
		return $this->queries;
	}

	public function getQueryList() {
		return $this->queryList;
	}

	public function listTables() {
		return $this->tables;
	}

	public function optimize() {
		return $this->exec('VACUUM');
	}

	public function query($query) {
		try {
			$this->queries++;
			$this->queryList[] = $query;
			$result = $this->db->query($query);
			return $result;
		} catch (PDOException $e) {
			FW_Exception::showError($e);
		}
	}

	public function quote($string) {
		return $this->db->quote($string);
	}

	public function readEntry($table,$columns,$expression=array(),$options=array()) {
		$sql = 'SELECT ';
		if (isset($options['count']) AND $options['count'] === TRUE) $sql .= 'count(';
		if (is_array($columns)) {
			$count = count($columns)-1;
			$i = 0;
			foreach($columns as $column) {
				$sql .= $column;
				if($i != $count) $sql .= ',';
				$i++;
			}
		} else {
			if ($columns == '*') $sql .= '*';
			else $sql .= $columns;
		}
		if (isset($options['count']) AND $options['count'] === TRUE) $sql .= ')';
		$sql .= ' FROM \''.$table.'\'';
		if (!empty($expression)) $sql .= ' WHERE';
		if (isset($expression['column'])) $sql .= ' '.$expression['column'];
		if (isset($expression['operator'])) $sql .= ' '.$expression['operator'];
		if (isset($expression['value'])) $sql .= ' '.$this->quote($expression['value']);
		if (isset($expression['and'])) {
			$sql .= ' AND ';
			if (isset($expression['and']['column'])) $sql .= ' '.$expression['and']['column'];
			if (isset($expression['and']['operator'])) $sql .= ' '.$expression['and']['operator'];
			if (isset($expression['and']['value'])) $sql .= ' '.$this->quote($expression['and']['value']);
		}
		if (isset($options['sortColumn'])) {
			$sql .= ' ORDER BY \''.$options['sortColumn'].'\'';
			if (isset($options['sortOrder'])) {
				if (strtolower($options['sortOrder']) == 'desc') $sql .= ' DESC';
				else $sql .= ' ASC';
			}
		}
		if (isset($options['limitOffset'])) {
			$sql .= ' LIMIT '.$options['limitOffset'];
			if (isset($options['limitLength'])) $sql .= ', '.$options['limitLength'];
		}
		$sql .= ';';
		$data = array();
		$i = 0;
		//print $sql;
		$result = $this->query($sql);
		if (!$result) return $data;
		foreach ($result as $row) {
			$data[$i] = array();
			foreach($row as $key => $value) {
				if (is_numeric($value)) {
					if (is_float($value)) $data[$i][$key] = (float)$value;
					else $data[$i][$key] = (integer)$value;
				} else $data[$i][$key] = $value;
			}
			$i++;
		}
		return $data;
	}

}