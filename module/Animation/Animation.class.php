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
 * LIB211 Animation
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211Animation extends LIB211Base {

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

	private $gif = 'GIF89a'; #GIF-Daten
	private $buffer = array(); #GIF-Puffer
	private $loop =  0; #GIF-Schleifenanzahl
	private $disposal =  2; #GIF-Bereinigung
	private $color = -1; #GIF-Transparenzfarbe
	private $image = -1; #GIF-Image

	private $apiurl = NULL; #URL zu den Animationen
	private $api_bg = NULL; #Hintergrundfarben-Parameter
	private $api_fg = NULL; #Vordergrundfarben-Parameter
	private $api_in = NULL; #Animations-Parameter
	private $api_ty = NULL; #Transparenz-Parameter
	
	private $bg = NULL; #Hintergrundfarbe
	private $fg = NULL; #Vordergrundfarbe
	private $anipath = NULL; #Pfad zu den Animationen
	private $ty = NULL; #Transparenzfarbe
	
	public $data = NULL; #Daten-Array
	
	/**
	 * Check gif block
	 * @param string $block_global
	 * @param string $block_local
	 * @param integer $length
	 * @return boolean
	 */
	private function _GIFBlockCheck($block_global,$block_local,$length) {
		for ($i = 0; $i < $length; $i++) {
			if ($block_global{3 * $i + 0} != $block_local{3 * $i + 0} OR $block_global{3 * $i + 1} != $block_local{3 * $i + 1} OR $block_global{3 * $i + 2} != $block_local{3 * $i + 2}) {
				return FALSE;
			}
		}
		return TRUE;
	}
		
	/**
	 * Create gif data
	 * @param array $source
	 * @param array $delay
	 * @param integer $loop
	 * @param integer $disposal
	 * @param integer $red
	 * @param integer $green
	 * @param integer $blue
	 * @param string $mode
	 * @return boolean
	 */
	private function _GIFCreate($source,$delay,$loop,$disposal,$red,$green,$blue,$mode) {
		if (!is_array($source) AND !is_array($delay)) return $this->_GIFOutput(TRUE);
		$this->loop = ($loop > -1) ? $loop : 0;
		$this->disposal = ($disposal > -1) ? (($disposal < 3) ? $disposal : 3) : 2;
		$this->color = ($red > -1 AND $green > -1 AND $blue > -1) ? ($red | ($green << 8) | ($blue << 16)) : -1;
		for ($i = 0; $i < count($source); $i++) {
			if (strtolower($mode) == 'url') {
				$this->buffer[] = fread(fopen($source[$i],'rb'),filesize($source[$i]));
			} elseif (strtolower($mode) == 'bin') {
				$this->buffer[] = $source[$i];
			} else {
				return $this->_GIFOutput(TRUE);
			}
			if (substr($this->buffer[$i],0,6) != 'GIF87a' AND substr($this->buffer[$i],0,6) != 'GIF89a') {
				return $this->_GIFOutput(TRUE);
			}
			for ($j = (13 + 3 * (2 << (ord($this->buffer[$i]{10}) & 0x07))),$k = TRUE; $k; $j++) {
				switch($this->buffer[$i]{$j}) {
					case '!':
						if((substr($this->buffer[$i],($j + 3),8)) == 'NETSCAPE') {
							return $this->_GIFOutput(TRUE);
						}
					break;
					case ';':
						$k = FALSE;
					break;
				}
			}
		}
		$this->_GIFHeader();
		for ($i = 0; $i < count($this->buffer); $i++) {
			$this->_GIFFrame($i,$delay[$i]);
		}
		$this->_GIFFooter();
		return TRUE;
	}
	
	/**
	 * Create gif footer
	 * 
	 */
	private function _GIFFooter() {
		$this->gif .= ';';
	}
	
	/**
	 * Create gif frame
	 * @param integer $i
	 * @param integer $d
	 */
	private function _GIFFrame($i,$d) {
		$local_str = 13 + 3 * (2 << (ord($this->buffer[$i]{10}) & 0x07));
		$local_end = strlen($this->buffer[$i]) - $local_str - 1;
		$local_tmp = substr($this->buffer[$i],$local_str,$local_end);
		$global_len = 2 << (ord($this->buffer[0]{10}) & 0x07);
		$Locals_len = 2 << (ord($this->buffer[$i]{10}) & 0x07);
		$global_rgb = substr($this->buffer[0],13,3 * (2 << (ord($this->buffer[0] {10}) & 0x07)));
		$local_rgb = substr($this->buffer[$i],13,3 * (2 << (ord($this->buffer[$i]{10}) & 0x07)));
		$local_ext = "!\xF9\x04".chr(($this->disposal << 2) + 0).chr(($d >> 0) & 0xFF).chr( ($d >> 8) & 0xFF)."\x0\x0";
		if($this->color > -1 AND ord($this->buffer[$i]{10}) & 0x80) {
			for ($j = 0; $j < (2 << (ord ($this->buffer[$i]{10}) & 0x07)); $j++) {
				if (ord($local_rgb{3 * $j + 0}) == (($this->color >> 16) & 0xFF) AND ord($local_rgb{3 * $j + 1}) == (($this->color >>  8) & 0xFF) AND ord($local_rgb{3 * $j + 2}) == (($this->color >>  0) & 0xFF)) {
					$local_ext = "!\xF9\x04".chr(($this->disposal << 2) + 1).chr(($d >> 0) & 0xFF).chr(($d >> 8) & 0xFF).chr($j)."\x0";
					break;
				}
			}
		}
		switch($local_tmp{0}) {
			case '!':
				$local_img = substr($local_tmp,8,10);
				$local_tmp = substr($local_tmp,18,strlen($local_tmp) - 18);
			break;
			case ',':
				$local_img = substr($local_tmp,0,10);
				$local_tmp = substr($local_tmp,10,strlen($local_tmp) - 10);
			break;
		}
		if (ord($this->buffer[$i]{10}) & 0x80 && $this->image > -1) {
			if ($global_len == $Locals_len) {
				if ($this->_GIFBlockCheck($global_rgb,$local_rgb,$global_len)) {
					$this->gif .= ($local_ext.$local_img.$local_tmp);
				} else {
					$byte  = ord($local_img{9});
					$byte |= 0x80;
					$byte &= 0xF8;
					$byte |= (ord($this->buffer[0]{10}) & 0x07);
					$local_img{9} = chr($byte);
					$this->gif .= ($local_ext.$local_img.$local_rgb.$local_tmp);
				}
			} else {
				$byte = ord($local_img{9});
				$byte |= 0x80;
				$byte &= 0xF8;
				$byte |= (ord($this->buffer[$i]{10}) & 0x07);
				$local_img{9} = chr($byte);
				$this->gif .= ($local_ext.$local_img.$local_rgb.$local_tmp);
			}
		} else {
			$this->gif .= $local_ext.$local_img.$local_tmp;
		}
		$this->image  = 1;
	}
	
	/**
	 * Create gif header
	 */
	private function _GIFHeader() {
		$cmap = 0;
		if (ord($this->buffer[0]{10}) & 0x80) {
			$cmap = 3 * (2 << (ord($this->buffer[0]{10}) & 0x07));
			$this->gif .= substr($this->buffer[0],6,7);
			$this->gif .= substr($this->buffer[0],13,$cmap);
			$this->gif .= "!\377\13NETSCAPE2.0\3\1".$this->_GIFWord($this->loop)."\0";
		}
	}
	
	/**
	 * Output gif data
	 * @param boolean $error
	 * @return string
	 */
	private function _GIFOutput($error=FALSE) {
		$data = $this->gif;
		$this->gif = 'GIF89a';
		$this->buffer = array();
		$this->loop =  0;
		$this->disposal =  2;
		$this->color = -1;
		$this->image = -1;
		if(!$error) return($data);
	}
	
	/**
	 * Convert to gif word
	 * @param integer $int
	 * @return boolean
	 */
	private function _GIFWord($int) {
		return(chr($int & 0xFF).chr(($int >> 8) & 0xFF));
	}
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/tmp/.lock/LIB211Animation')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211AnimationException');
			touch(LIB211_ROOT.'/tmp/.lock/LIB211Animation',time());
		}
		self::$instances++;
		self::$time_start = microtime(TRUE);

		$this->anipath = LIB211_ROOT.'/module/Animation/animation';
		$this->apiurl = 'Animation.php';
		$this->api_bg = 'bg';
		$this->api_fg = 'fg';
		$this->api_in = 'in';
		$this->api_ty = 'ty';
		
		$this->bg = 0xFFFFFF;
		$this->fg = 0x000000;
		$this->ty = 'none';
		$this->data = array();
		
		if (!file_exists(LIB211_ROOT.'/module/Animation/cache')) mkdir(LIB211_ROOT.'/module/Animation/cache');
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
	 * Get a list of available animations
	 * @return array
	 */
	public function apiList() {
		$handler = opendir($this->anipath);
		$data = array();
		while(($e = readdir($handler))!==FALSE) {
			if ($e != '.' AND $e != '..' AND is_dir($this->anipath.'/'.$e)) {
				$data[] = $e;
			}
		}
		closedir($handler);
		sort($data,SORT_STRING);
		return $data;
	}
	
	/**
	 * Setup animation api
	 * @param mixed $anipath
	 * @param mixed $apiurl
	 * @param mixed $in
	 * @param mixed $bg
	 * @param mixed $fg
	 * @param mixed $ty
	 * @return boolean
	 */
	public function apiSetup($anipath=NULL,$apiurl=NULL,$in=NULL,$bg=NULL,$fg=NULL,$ty=NULL) {
		if ($anipath===NULL) $anipath = LIB211_ROOT.'/module/Animation/animation';
		if ($apiurl===NULL) $apiurl = 'Animation.php';
		if ($in===NULL) $in = 'in';
		if ($bg===NULL) $bg = 'bg';
		if ($fg===NULL) $fg = 'fg';
		if ($ty===NULL) $ty = 'ty';
		if (!file_exists($anipath) OR !is_dir($anipath)) return FALSE;
		$this->anipath = $anipath;
		$this->apiurl = $apiurl;
		$this->api_bg = $bg;
		$this->api_fg = $fg;
		$this->api_in = $in;
		$this->api_ty = $ty;
		return TRUE;
	}
	
	/**
	 * Get animation api url
	 * @param string $animation
	 * @param string $type
	 * @return string
	 */
	public function apiURL($animation,$type='html') {
		if ($type=='html') $sep = '&amp;';
		else $sep = '&';
		$failurl = $this->apiurl.'?'.
		$this->api_in.'=test'.$sep.
		$this->api_bg.'='.dechex($this->bg).$sep.
		$this->api_fg.'='.dechex($this->fg).$sep.
		$this->api_ty.'='.$this->ty;
		if (!file_exists($this->anipath.'/'.$animation)) return $failurl;
		$url = $this->apiurl.'?'.
		$this->api_in.'='.$animation.$sep.
		$this->api_bg.'='.dechex($this->bg).$sep.
		$this->api_fg.'='.dechex($this->fg).$sep.
		$this->api_ty.'='.$this->ty;
		return $url;
	}
	
	/**
	 * Set background color
	 * @param integer $color
	 * @return boolean
	 */
	public function background($color=0xFFFFFF) {
		if (!empty($this->data)) $this->data['info']['bg'] = $color;
		$this->bg = $color;
		return TRUE;
	}
	
	/**
	 * Set foreground color
	 * @param integer $color
	 * @return boolean
	 */
	public function foreground($color=0x000000) {
		if (empty($this->data)) $this->data['info']['fg'] = $color;
		$this->fg = $color;
		return TRUE;
	}

	/**
	 * Create gif data
	 * @param array $source
	 * @param array $delay
	 * @param integer $loop
	 * @param integer $disposal
	 * @param integer $red
	 * @param integer $green
	 * @param integer $blue
	 * @param string $mode
	 * @return boolean
	 */
	public function GIFCreate($source,$delay,$loop,$disposal,$red,$green,$blue,$mode) {
		return $this->_GIFCreate($source,$delay,$loop,$disposal,$red,$green,$blue,$mode);
	}
	
	/**
	 * Output gif data
	 * @param boolean $error
	 * @return string
	 */
	public function GIFOutput($error=FALSE) {
		return $this->_GIFOutput($error);
	}

	/**
	 * Load animation
	 * @param string $animation
	 * @return boolean
	 */
	public function load($animation) {
		$this->data = array();
		$frame = 0;
		if (!file_exists($animation)) return FALSE;
		if (!is_dir($animation)) return FALSE;
		if (!file_exists($animation.'/animation.ini')) return FALSE;
		$ini = parse_ini_file($animation.'/animation.ini',TRUE);
		ksort($ini,SORT_STRING);
		if (!isset($ini['info'])) return FALSE;
		if (!isset($ini['info']['loop'])) return FALSE;
		if (!isset($ini['info']['disposal'])) return FALSE;
		foreach($ini as $section => $data) {
			if (preg_match('/^frame[0-9]+$/',$section)===1) {
				if (!isset($data['source'])) return FALSE;
				if (!isset($data['delay'])) return FALSE;
				$file = $animation.'/'.$data['source'];
				if (!file_exists($file)) return FALSE;
				if (!is_file($file)) return FALSE;
				$extension = str_replace('.','',strrchr($file,'.'));
				if ($extension != 'png') return FALSE;
				if(!($info = getimagesize($file,$info))) return FALSE;
				if ($info[2]!=IMAGETYPE_PNG) return FALSE;
				if ($info['bits']!=8) return FALSE;
				if ($info['mime']!='image/png') return FALSE;
				if(!($handler = imagecreatefrompng($file))) return FALSE;
				$this->data[$frame]['frame'] = $section;
				$this->data[$frame]['delay'] = $data['delay'];
				for($y = 0; $y < $info[1]; $y++) {
					for($x = 0; $x < $info[0]; $x++) {
						$pixel = imagecolorat($handler,$x,$y);
						if ($pixel == 0xFFFFFF) $this->data[$frame]['grid'][$y][$x] = 0;
						elseif ($pixel == 0x000000) $this->data[$frame]['grid'][$y][$x] = 1;
						else $this->data[$frame]['grid'][$y][$x] = 1;
					}
				}
				imagedestroy($handler);
				$frame++;
			}
		}
		$this->data['info']['width'] = $info[0];
		$this->data['info']['height'] = $info[1];
		$this->data['info']['fg'] = $this->fg;
		$this->data['info']['bg'] = $this->bg;
		$this->data['info']['ty'] = $this->ty;
		$this->data['info']['loop'] = (integer)$ini['info']['loop'];
		$this->data['info']['disposal'] = (integer)$ini['info']['disposal'];
		return TRUE;
	}

	/**
	 * Set transparency
	 * @param string $what
	 * @return boolean
	 */
	public function transparency($what='background') {
		switch($what) {
			case 'foreground': case 'fg':
				if (!empty($this->data)) $this->data['info']['ty'] = 'foreground';
				$this->ty = 'foreground';
			break;
			case 'background': case 'bg':
				if (!empty($this->data)) $this->data['info']['ty'] = 'background';
				$this->ty = 'background';
			break;
			default:
				if (!empty($this->data)) $this->data['info']['ty'] = 'none';
				$this->ty = 'none';
			break;
		}
		return TRUE;
	}

	/**
	 * Write animation
	 * @param mixed $file
	 * @return mixed
	 */
	public function write($file=NULL) {
		if (empty($this->data)) return FALSE;
		$source = array();
		$delays = array();
		$mode = 'bin';
		$bg = $this->bg;
		$fg = $this->fg;
		$loop = $this->data['info']['loop'];
		$disposal = $this->data['info']['disposal'];
		$width = $this->data['info']['width'];
		$height = $this->data['info']['height'];
		if ($this->ty=='background') $tcolor = str_pad(base_convert($bg,10,16),6,0,STR_PAD_LEFT);
		elseif ($this->ty=='foreground') $tcolor = str_pad(base_convert($fg,10,16),6,0,STR_PAD_LEFT);
		if ($this->ty == 'none') {
			$red = -1;
			$green = -1;
			$blue = -1;
		} else {
			$red = base_convert(substr($tcolor,0,2),16,10);
			$green = base_convert(substr($tcolor,2,2),16,10);
			$blue = base_convert(substr($tcolor,4,2),16,10);
		}
		foreach($this->data as $frame => $data) {
			if (!is_integer($frame)) continue;
			if (!($handler = imagecreatetruecolor($width,$height))) return FALSE;
			if ($this->ty=='background') imagecolortransparent($handler,$bg);
			elseif ($this->ty=='foreground') imagecolortransparent($handler,$fg);
			for($y = 0; $y <$height; $y++) {
				for($x = 0; $x < $width; $x++) {
					if ($this->data[$frame]['grid'][$y][$x]==0) imagesetpixel($handler,$x,$y,$bg);
					elseif ($this->data[$frame]['grid'][$y][$x]==1) imagesetpixel($handler,$x,$y,$fg);
					else imagesetpixel($handler,$x,$y,$fg);
				}
			}
			ob_start();
			imagegif($handler);
			$buffer = ob_get_contents();
			imagedestroy($handler);
			ob_end_clean();
			$source[$frame] = $buffer;
			$delays[$frame] = (integer)$this->data[$frame]['delay'];
		}
		$this->_GIFCreate($source,$delays,$loop,$disposal,$red,$green,$blue,$mode);
		if ($file===NULL) return $this->_GIFOutput(FALSE);
		else {
			$handler = fopen($file,'w');
			$buffer = $this->_GIFOutput(FALSE);
			fwrite($handler,$buffer,strlen($buffer));
			fclose($handler);
			return TRUE;
		}
	}

}

/**
 * LIB211 Animation Exception
 * 
 * @author C!$C0^211
 * @package LIB211
 */
class LIB211AnimationException extends LIB211BaseException {
}