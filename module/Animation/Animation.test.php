<?php
/**
 * @package LIB211
 */

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

/**
 * Include required files 
 */
if (LIB211_AUTOLOAD === FALSE) {
	require_once(LIB211_ROOT.'/module/Animation/Animation.class.php');
}

/**
 * LIB211 Animation Testclass
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211AnimationTest extends LIB211Testclass {
	
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
		$this->animation->background();
		$this->animation->foreground();
		$this->animation->transparency('none');
	}
	
	/**
	 * Execute before all methods
	 */
	public function setPrefixAll() {
		$this->animation = new LIB211Animation();
		$this->animation->apiSetup(LIB211_ROOT.'/module/Animation/animation','module/Animation/Animation.php','in','bg','fg','ty');
		$this->animations = $this->animation->apiList();
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

	/**
	 *  Test api animations
	 */
	public function testApi() {
		foreach ($this->animations as $animation) {
			print $animation.'.gif: <img src="/module/Animation/Animation.php?in='.$animation.'" alt="['.$animation.'.gif]"/><br/>';
		}
	}
	
	/**
	 *  Test cached animations
	 */
	public function testApiCached() {
		$animations = scandir(LIB211_ROOT.'/module/Animation/cache');
		unset($animations[0],$animations[1]);
		foreach ($animations as $animation) {
			print '<img src="/module/Animation/cache/'.$animation.'" alt="['.$animation.']" title="'.$animation.'"/> ';
		}
	}
	
	/**
	 *  Test apiList() method
	 */
	public function testApiList() {
		$animations = $this->animation->apiList();
		$checklist = scandir(LIB211_ROOT.'/module/Animation/animation');
		unset($checklist[0],$checklist[1]);
		foreach ($animations as $animation) {
			$this->assertTrue(in_array($animation,$checklist));
			print $animation.'<br/>';
		}
	}
	
	/**
	 *  Test apiURL() method
	 */
	public function testApiURL() {
		foreach ($this->animations as $animation) {
			print $animation.'.gif: <img src="'.$this->animation->apiURL($animation).'" alt="['.$animation.'.gif]"/><br/>';
		}
	}
	
	/**
	 *  Test background() method
	 */
	public function testBackground() {
		$colors = array(0xFFFFFF,0x000000,0xFF0000,0x00FF00,0x0000FF);
		foreach ($colors as $color) {
			$this->animation->background($color);
			foreach ($this->animations as $animation) {
				print $animation.' with background color '.$color.': <img src="'.$this->animation->apiURL($animation).'" alt="['.$animation.'.gif]"/><br/>';
			}
			print '<br/>';
		}
	}
	
	/**
	 *  Test foreground() method
	 */
	public function testForeground() {
		$colors = array(0xFFFFFF,0x000000,0xFF0000,0x00FF00,0x0000FF);
		foreach ($colors as $color) {
			$this->animation->foreground($color);
			foreach ($this->animations as $animation) {
				print $animation.' with foreground color '.$color.': <img src="'.$this->animation->apiURL($animation).'" alt="['.$animation.'.gif]"/><br/>';
			}
			print '<br/>';
		}
	}
	
	/**
	 *  Test load() method
	 */
	public function testLoad() {
		foreach ($this->animations as $animation) {
			$this->animation->load(LIB211_ROOT.'/module/Animation/animation/'.$animation);
			$data = $this->animation->write();
			print $animation.'.gif: <img src="data:image/gif;base64,'.base64_encode($data).'" alt="['.$animation.'.gif]"/><br/>';
		}
	}

	/**
	 *  Test transparency() method
	 */
	public function testTransparency() {
		$modes = array('foreground','background','none');
		foreach ($modes as $mode) {
			$this->animation->transparency($mode);
			foreach ($this->animations as $animation) {
				print $animation.' with '.$mode.' transparency: <img src="'.$this->animation->apiURL($animation).'" alt="['.$animation.'.gif]"/><br/>';
			}
			print '<br/>';
		}
	}
	
	/**
	 *  Test write() method
	 */
	public function testWrite() {
		$this->testLoad();
	}
	
}