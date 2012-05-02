<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/XML/XML.class.php');
}

define('EVEONLINE_ID_ALLIANCE',16159);
define('EVEONLINE_ID_CHARACTER',1377);
define('EVEONLINE_ID_CONSTELLATION',4);
define('EVEONLINE_ID_CORPORATION',2);
define('EVEONLINE_ID_REGION',3);
define('EVEONLINE_ID_SOLARSYSTEM',5);
define('EVEONLINE_ID_STATION',3867);

/**
 * LIB211 EVE Online
 *
 * @author C!$C0^211
 *
 */
class LIB211EVEOnline extends LIB211XML {

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

	private $useragentPattern = '/^Mozilla\/5.* AppleWebKit\/[0-9]{1,}.* Safari\/[0-9]{1,}.* EVE-IGB$/';

	private $isIngame = FALSE;

	private $isTrusted = FALSE;

	private $dbHandler = NULL;

	private $dbPath = NULL;

	private $timeout = 86400;
	#private $timeout = 0;

	/**
	 * Constructor
	 */
	public function __construct($dbPath = NULL) {
		parent::__construct();
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211EVEOnline')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211EVEOnlineException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211EVEOnline',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);
		if (preg_match($this->useragentPattern,$_SERVER['HTTP_USER_AGENT']) === 1) $this->isIngame = TRUE;
		else $this->isIngame = FALSE;
		if (isset($_SERVER['HTTP_EVE_TRUSTED']) AND $_SERVER['HTTP_EVE_TRUSTED'] == 'Yes') $this->isTrusted = TRUE;
		else $this->isTrusted = FALSE;
		if ($dbPath === NULL) $dbPath = LIB211_ROOT.'/module/EVEOnline';
		$this->dbPath = $dbPath;
		$create = FALSE;
		if (!file_exists($dbPath.'/LIB211EVEOnline.sqlite3.db')) {
			touch($dbPath.'/LIB211EVEOnline.sqlite3.db');
			$create = TRUE;
		}
		$this->dbHandler = @sqlite_open($dbPath.'/LIB211EVEOnline.sqlite3.db',0775,$error);
		if (!empty($error)) throw new LIB211EVEOnlineException($error);
		if ($create) {
			$result = sqlite_query($this->dbHandler,<<<ENDSQL
CREATE TABLE idcache (
	name	TEXT,
	id		VARCHAR(255),
	time	TIMESTAMP
);
ENDSQL
			);
			if (!$result) {
				throw new LIB211EVEOnlineException(sqlite_error_string(sqlite_last_error($this->dbHandler)));
			}
			$raw = file_get_contents('http://eve-files.com/chribba/typeid.txt');
			$lines = explode(EOL,$raw);
			foreach($lines as $line) {
				//print '<pre>';print $line;print '</pre>';
				if (preg_match('/^([0-9]+)[ ]+(.*)$/',$line,$m) === 1) {
					//print '<pre>';var_dump($m);print '</pre>';
					#$now = time();
					$now = mktime(0,0,0,0,0,2037);
					$id = sqlite_escape_string($m[1]);
					$name = sqlite_escape_string($m[2]);
					$result = @sqlite_unbuffered_query($this->dbHandler,<<<ENDSQL
INSERT INTO idcache (name,id,time) VALUES ('{$name}','{$id}',{$now});
ENDSQL
					);
					if (!$result) throw new LIB211EVEOnlineException(sqlite_error_string(sqlite_last_error($this->dbHandler)));
				}
			}


		}
		if (!file_exists($dbPath.'/image')) mkdir($dbPath.'/image');
		if (!file_exists($dbPath.'/image/alliance')) mkdir($dbPath.'/image/alliance');
		if (!file_exists($dbPath.'/image/character')) mkdir($dbPath.'/image/character');
		if (!file_exists($dbPath.'/image/corporation')) mkdir($dbPath.'/image/corporation');
		if (!file_exists($dbPath.'/image/inventory')) mkdir($dbPath.'/image/inventory');
		if (!file_exists($dbPath.'/image/render')) mkdir($dbPath.'/image/render');
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		@sqlite_close($this->dbHandler);
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

	public function getAllianceID() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_ALLIANCEID'])) {
			if ($_SERVER['HTTP_EVE_ALLIANCEID'] == 'None') return -1;
			else return strip_tags((string)$_SERVER['HTTP_EVE_ALLIANCEID']);
		} else {
			return '-1';
		}
	}

	public function getAllianceImage($id,$size) {
		switch($size){
			case 30: case 32: case 64: case 128:
			break;
			default:
				$size = 64;
			break;
		}
		$file = $id.'_'.$size.'.png';
		$path = $this->dbPath.'/image/alliance/'.$file;
		if (!file_exists($path)) {
			$data = file_get_contents('http://image.eveonline.com/Alliance/'.$file);
			$image = imagecreatefromstring($data);
			imagepng($image,$path);
			imagedestroy($image);
			return file_get_contents($path);
		} else {
			if ((filemtime($path)+$this->timeout) < time()) {
				unlink($path);
				$data = file_get_contents('http://image.eveonline.com/Alliance/'.$file);
				$image = imagecreatefromstring($data);
				imagepng($image,$path);
				imagedestroy($image);
				return file_get_contents($path);
			} else {
				return file_get_contents($path);
			}
		}
	}

	public function getAllianceName() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_ALLIANCENAME'])) {
			if ($_SERVER['HTTP_EVE_ALLIANCENAME'] == 'None') return '';
			else return strip_tags((string)$_SERVER['HTTP_EVE_ALLIANCENAME']);
		} else {
			return '';
		}
	}

	public function getCharacterID() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_CHARID'])) return strip_tags((string)$_SERVER['HTTP_EVE_CHARID']);
		else return '-1';
	}

	public function getCharacterImage($id,$size) {
		switch($size){
			case 30: case 32: case 64: case 128:
			case 200: case 256: case 512: case 1024:
			break;
			default:
				$size = 64;
			break;
		}
		$file = $id.'_'.$size.'.jpg';
		$path = $this->dbPath.'/image/character/'.$file;
		if (!file_exists($path)) {
			$data = file_get_contents('http://image.eveonline.com/Character/'.$file);
			$image = imagecreatefromstring($data);
			imagejpeg($image,$path);
			imagedestroy($image);
			return file_get_contents($path);
		} else {
			if ((filemtime($path)+$this->timeout) < time()) {
				unlink($path);
				$data = file_get_contents('http://image.eveonline.com/Character/'.$file);
				$image = imagecreatefromstring($data);
				imagejpeg($image,$path);
				imagedestroy($image);
				return file_get_contents($path);
			} else {
				return file_get_contents($path);
			}
		}
	}

	public function getCharacterName() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_CHARNAME'])) return strip_tags((string)$_SERVER['HTTP_EVE_CHARNAME']);
		else return '';
	}

	public function getConstellationID($name = NULL) {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if ($name === NULL) $name = $this->getConstellationName();
		return strip_tags($this->getIDFromName($name));
	}

	public function getConstellationName() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_CONSTELLATIONNAME'])) return strip_tags((string)$_SERVER['HTTP_EVE_CONSTELLATIONNAME']);
		else return '';
	}

	public function getCorporationID() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_CORPID'])) return strip_tags((string)$_SERVER['HTTP_EVE_CORPID']);
		else return '-1';
	}

	public function getCorporationImage($id,$size) {
		switch($size){
			case 30: case 32: case 64: case 128: case 256:
			break;
			default:
				$size = 64;
			break;
		}
		$file = $id.'_'.$size.'.png';
		$path = $this->dbPath.'/image/corporation/'.$file;
		if (!file_exists($path)) {
			$data = file_get_contents('http://image.eveonline.com/Corporation/'.$file);
			$image = imagecreatefromstring($data);
			imagepng($image,$path);
			imagedestroy($image);
			return file_get_contents($path);
		} else {
			if ((filemtime($path)+$this->timeout) < time()) {
				unlink($path);
				$data = file_get_contents('http://image.eveonline.com/Corporation/'.$file);
				$image = imagecreatefromstring($data);
				imagepng($image,$path);
				imagedestroy($image);
				return file_get_contents($path);
			} else {
				return file_get_contents($path);
			}
		}
	}

	public function getCorporationName() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_CORPNAME'])) return strip_tags((string)$_SERVER['HTTP_EVE_CORPNAME']);
		else return '';
	}

	public function getCorporationRole() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_CORPROLE'])) return strip_tags((string)$_SERVER['HTTP_EVE_CORPROLE']);
		else return '0';
	}

	public function getIDFromName($name) {
		$cname = sqlite_escape_string($name);
		$result = @sqlite_query($this->dbHandler,<<<ENDSQL
SELECT * FROM idcache WHERE name='{$cname}' LIMIT 1;
ENDSQL
		);
		if (!$result) throw new LIB211EVEOnlineException(sqlite_error_string(sqlite_last_error($this->dbHandler)));
		$entry = sqlite_fetch_array($result);
		#create
		if (!$entry) {
			$data = $this->import(file_get_contents('http://api.eve-online.com/eve/CharacterID.xml.aspx?names='.urlencode($name)));
			if (isset($data['eveapi']['result']['rowset']['row']['@attributes']['characterID']) AND
				$data['eveapi']['result']['rowset']['row']['@attributes']['characterID'] > 0) {
				$id = (string)$data['eveapi']['result']['rowset']['row']['@attributes']['characterID'];
				$now = time();
				$result = @sqlite_query($this->dbHandler,<<<ENDSQL
INSERT INTO idcache (name,id,time) VALUES ('{$cname}','{$id}',{$now});
ENDSQL
				);
				if (!$result) throw new LIB211EVEOnlineException(sqlite_error_string(sqlite_last_error($this->dbHandler)));
			}
			else $id = '-1';
		#read
		} else {
			#update
			if(($entry['time']+$this->timeout) < time()) {
				$data = $this->import(file_get_contents('http://api.eve-online.com/eve/CharacterID.xml.aspx?names='.urlencode($name)));
				if (isset($data['eveapi']['result']['rowset']['row']['@attributes']['characterID']) AND
					$data['eveapi']['result']['rowset']['row']['@attributes']['characterID'] > 0) {
					$id = (string)$data['eveapi']['result']['rowset']['row']['@attributes']['characterID'];
					$now = time();
					$result = @sqlite_query($this->dbHandler,<<<ENDSQL
UPDATE idcache SET id='{$id}', time={$now} WHERE name='{$cname}';
ENDSQL
					);
					if (!$result) throw new LIB211EVEOnlineException(sqlite_error_string(sqlite_last_error($this->dbHandler)));
				}
				else $id = '-1';
			#view
			} else {
				$id = $entry['id'];
			}
		}
		return strip_tags($id);
	}

	public function getInventoryImage($id,$size) {
		switch($size){
			case 32: case 64:
			break;
			default:
				$size = 64;
			break;
		}
		$file = $id.'_'.$size.'.png';
		$path = $this->dbPath.'/image/inventory/'.$file;
		if (!file_exists($path)) {
			$data = file_get_contents('http://image.eveonline.com/InventoryType/'.$file);
			$image = imagecreatefromstring($data);
			imagepng($image,$path);
			imagedestroy($image);
			return file_get_contents($path);
		} else {
			if ((filemtime($path)+$this->timeout) < time()) {
				unlink($path);
				$data = file_get_contents('http://image.eveonline.com/InventoryType/'.$file);
				$image = imagecreatefromstring($data);
				imagepng($image,$path);
				imagedestroy($image);
				return file_get_contents($path);
			} else {
				return file_get_contents($path);
			}
		}
	}

	public function getNameFromID($id) {
		$result = @sqlite_query($this->dbHandler,<<<ENDSQL
SELECT * FROM idcache WHERE id='{$id}' LIMIT 1;
ENDSQL
		);
		if (!$result) throw new LIB211EVEOnlineException(sqlite_error_string(sqlite_last_error($this->dbHandler)));
		$entry = sqlite_fetch_array($result);
		#create
		if (!$entry) {
			$data = $this->import(file_get_contents('http://api.eve-online.com/eve/CharacterName.xml.aspx?ids='.urlencode($id)));
			if (isset($data['eveapi']['result']['rowset']['row']['@attributes']['name']) AND
				$data['eveapi']['result']['rowset']['row']['@attributes']['name'] > 0) {
				$name = (string)$data['eveapi']['result']['rowset']['row']['@attributes']['name'];
				$now = time();
				$result = @sqlite_query($this->dbHandler,<<<ENDSQL
INSERT INTO idcache (name,id,time) VALUES ('{$name}','{$id}',{$now});
ENDSQL
				);
				if (!$result) throw new LIB211EVEOnlineException(sqlite_error_string(sqlite_last_error($this->dbHandler)));
			}
			else $name = 'Unknown';
		#read
		} else {
			#update
			if(($entry['time']+$this->timeout) < time()) {
				$data = $this->import(file_get_contents('http://api.eve-online.com/eve/CharacterName.xml.aspx?ids='.$id));
				if (isset($data['eveapi']['result']['rowset']['row']['@attributes']['name']) AND
					$data['eveapi']['result']['rowset']['row']['@attributes']['name'] > 0) {
					$name = (string)$data['eveapi']['result']['rowset']['row']['@attributes']['name'];
					$now = time();
					$result = @sqlite_query($this->dbHandler,<<<ENDSQL
UPDATE idtable SET name='{$name}', time={$now} WHERE id='{$id}';
ENDSQL
					);
					if (!$result) throw new LIB211EVEOnlineException(sqlite_error_string(sqlite_last_error($this->dbHandler)));
				}
				else $name = 'Unknown';
			#view
			} else {
				$name = $entry['name'];
			}
		}
		return strip_tags($name);
	}

	public function getRegionID($name = NULL) {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if ($name === NULL) $name = $this->getRegionName();
		return strip_tags($this->getIDFromName($name));
	}

	public function getRegionName() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_REGIONNAME'])) return strip_tags((string)$_SERVER['HTTP_EVE_REGIONNAME']);
		else return '';
	}

	public function getRenderImage($id,$size) {
		switch($size){
			case 32: case 64: case 128: case 256:  case 512:
			break;
			default:
				$size = 64;
			break;
		}
		$file = $id.'_'.$size.'.png';
		$path = $this->dbPath.'/image/render/'.$file;
		if (!file_exists($path)) {
			$data = file_get_contents('http://image.eveonline.com/Render/'.$file);
			$image = imagecreatefromstring($data);
			imagepng($image,$path);
			imagedestroy($image);
			return file_get_contents($path);
		} else {
			if ((filemtime($path)+$this->timeout) < time()) {
				unlink($path);
				$data = file_get_contents('http://image.eveonline.com/Render/'.$file);
				$image = imagecreatefromstring($data);
				imagepng($image,$path);
				imagedestroy($image);
				return file_get_contents($path);
			} else {
				return file_get_contents($path);
			}
		}
	}

	public function getServerIP() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_SERVERIP'])) return strip_tags((string)$_SERVER['HTTP_EVE_SERVERIP']);
		else return '';
	}

	public function getSolarsystemID($name = NULL) {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if ($name === NULL) $name = $this->getSolarsystemName();
		return strip_tags($this->getIDFromName($name));
	}

	public function getSolarsystemName() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_SOLARSYSTEMNAME'])) return strip_tags((string)$_SERVER['HTTP_EVE_SOLARSYSTEMNAME']);
		else return '';
	}

	public function getStationID() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_STATIONID'])) {
			if ($_SERVER['HTTP_EVE_STATIONID'] == 'None') return '-1';
			else return strip_tags((string)$_SERVER['HTTP_EVE_STATIONID']);
		} else {
			return '-1';
		}
	}

	public function getStationName() {
		if (!$this->isIngame() OR !$this->isTrusted()) return NULL;
		if (isset($_SERVER['HTTP_EVE_STATIONNAME'])) {
			if ($_SERVER['HTTP_EVE_STATIONNAME'] == 'None') return '';
			else return strip_tags((string)$_SERVER['HTTP_EVE_STATIONNAME']);
		} else {
			return '';
		}
	}

	public function isIngame($useragent = NULL) {
		if ($useragent === NULL) return $this->isIngame;
		if (preg_match($this->useragentPattern,$useragent) === 1) $this->isIngame = TRUE;
		else $this->isIngame = FALSE;
		return $this->isIngame;
	}

	public function isTrusted() {
		return $this->isTrusted;
	}

}

/**
 * LIB211 EVE Online Exception
 *
 * @author C!$C0^211
 *
 */
class LIB211EVEOnlineException extends LIB211BaseException {
}
