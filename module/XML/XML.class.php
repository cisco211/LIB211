<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
}

/**
 * LIB211 XML
 * 
 * @author C!$C0^211
 *
 */
class LIB211XML extends LIB211Base {

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
	 * Identifier for attributes
	 * @var string
	 */
	private $_attribKey = "@attributes";
	
	/**
	 * Identifier for cdata
	 * @var string
	 */
	private $_cdataKey = "@cdata";
	
	/**
	 * Last error
	 * @var string
	 */
	private $_error = "";
	
	/**
	 * Index iterator
	 * @var integer
	 */
	private $_index = 0;
	
	/**
	 * Error indicator
	 * @var boolean
	 */
	private $_isError = FALSE;
	
	/**
	 * Keys array
	 * @var array
	 */
	private $_keyArray = array();
	
	/**
	 * Array for parsed data
	 * @var array
	 */
	private $_parsed = array();
	
	/**
	 * Raw xml data
	 * @var string
	 */
	private $_rawXML = NULL;
	
	/**
	 * Values array
	 * @var array
	 */
	private $_valueArray = array();
	
	/**
	 * Identifier vor values
	 * @var string
	 */
	private $_valueKey = "@value";
	
	/**
	 * Data array
	 * @var array
	 */
	public $data = array();
	
	/**
	 * Encoding type
	 * @var string
	 */
	public $encoding = "UTF-8";
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/lib211.lock')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211XMLException');
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
		return $result;
	}

	/**
	 * Convert array to xml 
	 * @param string &$o
	 * @param integer &$s
	 * @param string $k
	 * @param string $v
	 * @param string $n
	 */
	private function _array2xml(&$o,&$s,$k,$v,$n=NULL) {
		if (empty($v)) return;
		foreach($v as $k_node => $v_node) {
			if ($k_node === $this->_valueKey) continue;
			if ($k_node === $this->_attribKey) continue;
			if ($k_node === $this->_cdataKey) continue;
			if(is_array($v_node) AND isset($v_node[0])) {
				$this->_array2xml($o,$s,$k_node,$v_node,$k_node);
				continue;
			}
			if ($n!==NULL AND is_integer($k_node)) $o .= $this->_indent($s)."<".$n;
			else $o .= $this->_indent($s)."<".$k_node;
			if (is_array($v_node) AND isset($v_node[$this->_attribKey])) {
				foreach($v_node[$this->_attribKey] as $k_attr => $v_attr) $o .= " ".$k_attr."=\"".$v_attr."\"";
			}
			$o .= ">";
			if (is_array($v_node) AND !isset($v_node[$this->_valueKey])) $o .= EOL;
			$s++;
			if (is_array($v_node) AND isset($v_node[$this->_valueKey])) $o .= $v_node[$this->_valueKey];
			elseif (is_array($v_node)) $this->_array2xml($o,$s,$k_node,$v_node,$k_node);
			else $o .= $v_node;
			if (is_array($v_node) AND !isset($v_node[$this->_valueKey])) {
				if ($n!==NULL AND is_integer($k_node)) $o .= $this->_indent($s-1)."</".$n.">".EOL;
				else $o .= $this->_indent($s-1)."</".$k_node.">".EOL;
			}
			else {
				if ($n!==NULL AND is_integer($k_node)) $o .= "</".$n.">".EOL;
				else $o .= "</".$k_node.">".EOL;
			}
			$s--;
		}
	}

	/**
	 * Return indend
	 * @param integer $x
	 * @return string
	 */
	private function _indent($x) {
		$s = "";
		for ($i = 0; $i < $x; $i++) $s .= "\t";
		return $s;
	}
	
	/**
	 * Parse xml
	 * @param string $xml
	 * @param string $encoding
	 * @return array
	 */
	private function _parse($xml=NULL,$encoding="UTF-8") {
		if (!is_null($xml)) $this->_rawXML = $xml;
		$this->_isError = FALSE;
		if (!$this->_parse_init($encoding)) return "";
		$this->_index = 0;
		$this->_parsed = $this->_parse_recurse();
		$this->_status = "parsing complete";
		return $this->_parsed;
	}

	/**
	 * Initialize parser
	 * @param string $encoding
	 * @return boolean
	 */
	private function _parse_init($encoding) {
		$this->encoding = $encoding;
		$this->_parser = xml_parser_create($this->encoding);
		$parser = $this->_parser;
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
		if (!$res = (bool)xml_parse_into_struct($parser,$this->_rawXML,$this->_valueArray,$this->_keyArray)) {
			$this->_isError = TRUE;
			$this->_error = "error: ".xml_error_string(xml_get_error_code($parser))." at line ".xml_get_current_line_number($parser);
			print $this->_error;
		}
		xml_parser_free($parser);
		return $res;
	}
	
	/**
	 * Parse xml data recursively
	 * @return array
	 */
	private function _parse_recurse() {
		$found = array();
		$tagCount = array();
		while (isset($this->_valueArray[$this->_index])) {
			$tag = $this->_valueArray[$this->_index];
			$this->_index++;
			if ($tag["type"] == "close") return $found;
			if ($tag["type"] == "cdata") {
				$tag["tag"] = $this->_cdataKey;
				$tag["type"] = "complete";
			}
			$tagName = $tag["tag"];
			if (isset($tagCount[$tagName])) {
				if ($tagCount[$tagName] == 1) $found[$tagName] = array($found[$tagName]);
				$tagRef =& $found[$tagName][$tagCount[$tagName]];
				$tagCount[$tagName]++;
			}
			else {
				$tagCount[$tagName] = 1;
				$tagRef =& $found[$tagName];
			}
			switch ($tag["type"]) {
				case "open":
					$tagRef = $this->_parse_recurse();
					if (isset($tag["attributes"])) $tagRef[$this->_attribKey] = $tag["attributes"];
					if (isset($tag["value"])) {
						if (isset($tagRef[$this->_cdataKey])) {
							$tagRef[$this->_cdataKey] = (array)$tagRef[$this->_cdataKey];
							array_unshift($tagRef[$this->_cdataKey],$tag["value"]);
						}
						else $tagRef[$this->_cdataKey] = htmlentities($tag["value"]);
					}
				break;
				case "complete":
					if (isset($tag["attributes"])) {
						$tagRef[$this->_attribKey] = $tag["attributes"];
						$tagRef =& $tagRef[$this->_valueKey];
					}
					if (isset($tag["value"])) $tagRef = htmlentities($tag["value"]);
				break;
			}
		}
		return $found;
	}

	/**
	 * Export to xml data
	 * @param string $xml
	 * @return string
	 */
	public function export($xml=NULL) {
		$o = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\r\n";
		$s = 0;
		$k = $this->root();
		$v = $this->data;
		$this->_array2xml($o,$s,$k,$v);
		if ($xml === NULL) return $o;
		else {
			$handler = fopen($xml,"w");
			fwrite($handler,$o,strlen($o));
			fclose($handler);
			return $o;
		}
		return "";
	}

	/**
	 * Import from xml data
	 * @param string $xml
	 * @return array
	 */
	public function import($xml) {
		$encoding = "UTF-8";
		$pattern = "/encoding=\"(.*)\"/";
		if (strpos($xml,"<?xml") !== FALSE) {
			if (preg_match($pattern,$xml,$m)===1) $this->data =$this->_parse($xml,$m[1]);
			else $this->data = $this->_parse($xml,$encoding);
		} else {
			if (file_exists($xml)) {
				$xml = file_get_contents($xml);
				if (preg_match($pattern,$xml,$m)===1)$this->data =$this->_parse($xml,$m[1]);
				else $this->data = $this->_parse($xml,$encoding);
			}
		}
		return $this->data;
	}

	/**
	 * Detect root element
	 * @return string
	 */
	public function root() {
		$root = "";
		if (empty($this->data)) return "";
		foreach($this->data as $key => $value) {
			$root = $key;
			break;
		}
		return $root;
	}

}

/**
 * LIB211 XML Exception
 * 
 * @author C!$C0^211
 *
 */
class LIB211XMLException extends LIB211BaseException {
}