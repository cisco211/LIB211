<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

// Include required files
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/EVEOnline/EVEOnline.class.php');
}

/**
 * LIB211 EVE Online Testclass
 *
 * @author C!$C0^211
 *
 */
class LIB211EVEOnlineTest extends LIB211Testclass {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute before each test method
	 */
	public function setPrefix() {
	}

	/**
	 * Execute before all methods
	 */
	public function setPrefixAll() {
		$this->eveonline = new LIB211EVEOnline();
	}

	/**
	 * Execute after each test method
	 */
	public function setSuffix() {
	}

	/**
	 * Execute afater all methods
	 */
	public function setSuffixAll() {
		unset($this->example);
	}

	public function testGetAllianceID() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_ALLIANCEID'])) {
				$this->assertEquals('-1',$this->eveonline->getAllianceID());
			} elseif ($_SERVER['HTTP_EVE_ALLIANCEID'] == 'None') {
				$this->assertEquals('-1',$this->eveonline->getAllianceID());
			} else {
				$this->assertEquals((string)$_SERVER['HTTP_EVE_ALLIANCEID'],$this->eveonline->getAllianceID());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getAllianceID());
		}
	}

	public function testGetAllianceImage() {
	}

	public function testGetAllianceName() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_ALLIANCENAME'])) {
				$this->assertEquals('',$this->eveonline->getAllianceName());
			} elseif ($_SERVER['HTTP_EVE_ALLIANCENAME'] == 'None') {
				$this->assertEquals('',$this->eveonline->getAllianceName());
			} else {
				$this->assertEquals($_SERVER['HTTP_EVE_ALLIANCENAME'],$this->eveonline->getAllianceName());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getAllianceName());
		}
	}

	public function testGetCharacterID() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_CHARID'])) {
				$this->assertEquals('-1',$this->eveonline->getCharacterID());
			} else {
				$this->assertEquals((string)$_SERVER['HTTP_EVE_CHARID'],$this->eveonline->getCharacterID());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getCharacterID());
		}
	}

	public function testGetCharacterImage() {
	}

	public function testGetCharacterName() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_CHARNAME'])) {
				$this->assertEquals('',$this->eveonline->getCharacterName());
			} else {
				$this->assertEquals($_SERVER['HTTP_EVE_CHARNAME'],$this->eveonline->getCharacterName());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getCharacterName());
		}
	}

	public function testGetConstellationID() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_CONSTELLATIONNAME'])) {
				$this->assertEquals('20000727',$this->eveonline->getConstellationID('Crux'));
			} else {
				$this->assertEquals($this->eveonline->getIDFromName($_SERVER['HTTP_EVE_CONSTELLATIONNAME']),$this->eveonline->getConstellationID());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getConstellationID(''));
		}
	}

	public function testGetConstellationName() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_CONSTELLATIONNAME'])) {
				$this->assertEquals('',$this->eveonline->getConstellationName());
			} else {
				$this->assertEquals($_SERVER['HTTP_EVE_CONSTELLATIONNAME'],$this->eveonline->getConstellationName());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getConstellationName());
		}
	}

	public function testGetCorporationID() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_CORPID'])) {
				$this->assertEquals('-1',$this->eveonline->getCorporationID());
			} else {
				$this->assertEquals((string)$_SERVER['HTTP_EVE_CORPID'],$this->eveonline->getCorporationID());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getCorporationID());
		}
	}

	public function testGetCorporationImage() {
	}

	public function testGetCorporationName() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_CORPNAME'])) {
				$this->assertEquals('',$this->eveonline->getCorporationName());
			} else {
				$this->assertEquals($_SERVER['HTTP_EVE_CORPNAME'],$this->eveonline->getCorporationName());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getCorporationName());
		}
	}

	public function testGetCorporationRole() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_CORPROLE'])) {
				$this->assertEquals('0',$this->eveonline->getCorporationRole());
			} else {
				$this->assertEquals((string)$_SERVER['HTTP_EVE_CORPROLE'],$this->eveonline->getCorporationRole());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getCorporationRole());
		}
	}

	public function testGetIDFromName() {
		$this->assertEquals('10000043',$this->eveonline->getIDFromName('Domain'));
		$this->assertEquals('20000727',$this->eveonline->getIDFromName('Crux'));
		$this->assertEquals('30002189',$this->eveonline->getIDFromName('Hedion'));
		$this->assertEquals('60011740',$this->eveonline->getIDFromName('Oursulaert III - Federation Navy Testing Facilities'));
		$this->assertEquals('1000084',$this->eveonline->getIDFromName('Amarr Navy'));
		$this->assertEquals('500003',$this->eveonline->getIDFromName('Amarr Empire'));
		$this->assertEquals('597',$this->eveonline->getIDFromName('Punisher'));
	}

	public function testGetInventoryImage() {
	}

	public function testGetNameFromID() {
		$this->assertEquals('Domain',$this->eveonline->getNameFromID('10000043'));
		$this->assertEquals('Crux',$this->eveonline->getNameFromID('20000727'));
		$this->assertEquals('Hedion',$this->eveonline->getNameFromID('30002189'));
		#$this->assertEquals('Oursulaert III - Federation Navy Testing Facilities',$this->eveonline->getIDFromName('60011740'));
		$this->assertEquals('Amarr Navy',$this->eveonline->getNameFromID('1000084'));
		$this->assertEquals('Amarr Empire',$this->eveonline->getNameFromID('500003'));
		$this->assertEquals('Punisher',$this->eveonline->getNameFromID('597'));
	}

	public function testGetRegionID() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_REGIONNAME'])) {
				$this->assertEquals('10000043',$this->eveonline->getRegionID('Domain'));
			} else {
				$this->assertEquals($this->eveonline->getIDFromName($_SERVER['HTTP_EVE_REGIONNAME']),$this->eveonline->getRegionID());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getRegionID(''));
		}
	}

	public function testGetRegionName() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_REGIONNAME'])) {
				$this->assertEquals('',$this->eveonline->getRegionName());
			} else {
				$this->assertEquals($_SERVER['HTTP_EVE_REGIONNAME'],$this->eveonline->getRegionName());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getRegionName());
		}
	}

	public function testGetRenderImage() {
	}

	public function testGetServerIP() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_SERVERIP'])) {
				$this->assertEquals('',$this->eveonline->getServerIP());
			} else {
				$this->assertEquals($_SERVER['HTTP_EVE_SERVERIP'],$this->eveonline->getServerIP());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getServerIP());
		}
	}

	public function testGetSolarsystemID() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_SOLARSYSTEMNAME'])) {
				$this->assertEquals('30002189',$this->eveonline->getSolarsystemID('Hedion'));
			} else {
				$this->assertEquals($this->eveonline->getIDFromName($_SERVER['HTTP_EVE_SOLARSYSTEMNAME']),$this->eveonline->getSolarsystemID());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getSolarsystemID(''));
		}
	}

	public function testGetSolarsystemName() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_SOLARSYSTEMNAME'])) {
				$this->assertEquals('',$this->eveonline->getSolarsystemName());
			} else {
				$this->assertEquals($_SERVER['HTTP_EVE_SOLARSYSTEMNAME'],$this->eveonline->getSolarsystemName());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getSolarsystemName());
		}
	}

	public function testGetStationID() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_STATIONID'])) {
				$this->assertEquals('-1',$this->eveonline->getStationID());
			} else {
				$this->assertEquals((string)$_SERVER['HTTP_EVE_STATIONID'],$this->eveonline->getStationID());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getStationID());
		}
	}

	public function testGetStationName() {
		if($this->eveonline->isIngame() AND $this->eveonline->isTrusted()) {
			if (!isset($_SERVER['HTTP_EVE_STATIONNAME'])) {
				$this->assertEquals('',$this->eveonline->getStationName());
			} else {
				$this->assertEquals($_SERVER['HTTP_EVE_STATIONNAME'],$this->eveonline->getStationName());
			}
		} else {
			$this->assertEquals(NULL,$this->eveonline->getStationName());
		}
	}

	public function testIsIngame() {
		if (preg_match('/^Mozilla\/5.* AppleWebKit\/[0-9]{1,}.* Safari\/[0-9]{1,}.* EVE-IGB$/',$_SERVER['HTTP_USER_AGENT']) === 1) {
			$this->assertTrue($this->eveonline->isIngame());
		} else {
			$this->assertFalse($this->eveonline->isIngame());
		}
		$this->assertTrue($this->eveonline->isIngame('Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/532.0 (KHTML, like Gecko) Chrome/3.0.195.27 Safari/532.0 EVE-IGB'));
		$this->assertFalse($this->eveonline->isIngame('Mozilla/5.0 (Windows NT 5.1) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.68 Safari/534.24'));
	}

	public function testIsTrusted() {
		if (isset($_SERVER['HTTP_EVE_TRUSTED']) AND $_SERVER['HTTP_EVE_TRUSTED'] == 'Yes') {
			$this->assertTrue($this->eveonline->isTrusted());
		} else {
			$this->assertFalse($this->eveonline->isTrusted());
		}
	}
}
