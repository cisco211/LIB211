<?php

// Security lock
if (!defined('LIB211_EXEC')) throw new Exception('Invalid access to LIB211.');

/**
 * LIB211 Geohash
 * 
 * @author C!$C0^211
 *
 */
class LIB211Geohash extends LIB211Base {

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
	public function __construct() {
		parent::__construct(); 
		if (!file_exists(LIB211_ROOT.'/lib211.lock')) {
			$this->__check('c','ErrorException');
			$this->__check('c','Exception');
			$this->__check('c','LIB211GeohashException');
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
	 * Get the string of a base64 position
	 * @param integer $vInput
	 * @return string|boolean
	 */
	private function _intToBase32($vInput){
    	$v = (int)$vInput;
    	if($v < 0 || $v > 31) return false;
    	$base32 = '0123456789bcdefghjkmnpqrstuvwxyz';
    	$string = substr($base32, $v, 1);
    	return $string;
	}

	/**
	 * Calculate the base32 position of a string
	 * @param string $stringInput
	 * @return integer|boolean
	 */
	private function _base32ToInt($stringInput){
	    if(strlen($stringInput) != 1) return false;
	    $string = $stringInput;
	    $base32 = '0123456789bcdefghjkmnpqrstuvwxyz';
	    $v = strpos($base32, $string);
	    return $v;
	}

	/**
	 * Decode the intervalls of a geohash
	 * @param string $geohashInput
	 * @return array|boolean
	 */
	public function decodeInterval($geohashInput) {
	    $geohash = $geohashInput;
	    $arrayLatitude = array(-90.0, 90.0);
	    $arrayLongitude = array(-180.0, 180.0);
	    $rLatitude = 90.0;
	    $rLongitude = 180.0;
	    $length = strlen($geohash);
	    if($length == 0) return false;
	    $arrayMask = array(16,8,4,2,1);
	    $isEven = true;
	    for($i=0; $i<$length; $i++){
	        $string = substr($geohash, $i, 1);
	        $v = $this->_base32ToInt($string);
	        if($v === false) return false;
	        foreach($arrayMask as $mask){
	            if($isEven){
	                $rLongitude /= 2;
	                if($v & $mask){
	                    $arrayLongitude = array(($arrayLongitude[0] + $arrayLongitude[1])/2, $arrayLongitude[1]);
	                }
	                else{
	                    $arrayLongitude = array($arrayLongitude[0], ($arrayLongitude[0] + $arrayLongitude[1])/2);
	                }
	            }
	            else{
	                $rLatitude /= 2;
	                if($v & $mask){
	                    $arrayLatitude = array(($arrayLatitude[0] + $arrayLatitude[1])/2, $arrayLatitude[1]);
	                }
	                else{
	                    $arrayLatitude = array($arrayLatitude[0], ($arrayLatitude[0] + $arrayLatitude[1])/2);
	                }
	            }
	            $isEven = !$isEven;
	        }
	    }
	    return array($arrayLatitude, $arrayLongitude);
	}

	/**
	 * Decode latitude and longitude of a geohash
	 * @param string $geohashInput
	 * @return array|boolean
	 */
	public function decode($geohashInput){
	    $arrayTmp = $this->decodeInterval($geohashInput);
	    if($arrayTmp == false) return false;
	    list($arrayLatitude, $arrayLongitude) = $arrayTmp;
	    $placesLatitude = max(1, -round(log10($arrayLatitude[1] - $arrayLatitude[0]))) - 1;
	    $placesLongitude = max(1, -round(log10($arrayLongitude[1] - $arrayLongitude[0]))) - 1;
	    $latitude = round(($arrayLatitude[0] + $arrayLatitude[1]) / 2, $placesLatitude);
	    $longitude = round(($arrayLongitude[0] + $arrayLongitude[1]) / 2, $placesLongitude);
	    return array($latitude, $longitude);
	}

	/**
	 * Encode latitude and longitude to geohash
	 * @param float $latitudeInput
	 * @param float $longitudeInput
	 * @param integer $lengthInput
	 * @return string|boolean
	 */
	public function encode($latitudeInput, $longitudeInput, $lengthInput=11){
	    $latitude = (float)$latitudeInput;
	    $longitude = (float)$longitudeInput;
	    if($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) return false;
	    $length = (int)$lengthInput;
	    if($length <= 0) return false;
	    $arrayLatitude = array(-90.0, 90.0);
	    $arrayLongitude = array(-180.0, 180.0);
	    $count = 0;
	    $stringBin = '';
	    while($count <= $length * 5){
	        $cLongitude = ($arrayLongitude[0] + $arrayLongitude[1]) / 2;
	        if($longitude < $cLongitude){
	            $stringBin .= '0';
	            $arrayLongitude[1] = $cLongitude;
	        }
	        else{
	            $stringBin .= '1';
	            $arrayLongitude[0] = $cLongitude;
	        }
	        $cLatitude = ($arrayLatitude[0] + $arrayLatitude[1]) / 2;
	
	        if($latitude < $cLatitude){
	            $stringBin .= '0';
	            $arrayLatitude[1] = $cLatitude;
	        }
	        else{
	            $stringBin .= '1';
	            $arrayLatitude[0] = $cLatitude;
	        }
	        $count++;
	    }
	    $stringGeohash = '';
	    for($i=0; $i<$length; $i++){
	        $stringSub = substr($stringBin, $i*5, 5);
	        $stringGeohash .= $this->_intToBase32(bindec($stringSub));
	    }
	    return $stringGeohash;
	}

	/**
	 * Calculate the neighbours of a geohash
	 * @param string $geohashInput
	 * @param integer $rangeInput
	 * @return array|boolean
	 */
	public function neighbour($geohashInput, $rangeInput=1){
	    $geohash = $geohashInput;
	    $length = strlen($geohash);
	    $range = (int)$rangeInput;
	    if($range < 1) return false;
	    $arrayTmp = $this->decodeInterval($geohash);
	    if($arrayTmp == false) return false;
	    list($arrayLatitude, $arrayLongitude) = $arrayTmp;
	    $deltaLatitude = $arrayLatitude[1] - $arrayLatitude[0];
	    $deltaLongitude = $arrayLongitude[1] - $arrayLongitude[0];
	    $latitude = ($arrayLatitude[0] + $arrayLatitude[1]) / 2;
	    $longitude = ($arrayLongitude[0] + $arrayLongitude[1]) / 2;
	    $arrayGeohash = array();
	    for($i=-1*$range; $i<=1*$range; $i++){
	        for($j=-1*$range; $j<=1*$range; $j++){
	            if($i == 0 && $j == 0) continue;
	            $tmpLatitude = $latitude + $deltaLatitude * $i;
	            if($tmpLatitude < -90.0) $tmpLatitude += 180.0;
	            else if($tmpLatitude > 90.0) $tmpLatitude -= 180.0;
	            $tmpLongitude = $longitude + $deltaLongitude * $j;
	            if($tmpLongitude < -180.0) $tmpLongitude += 360.0;
	            else if($tmpLongitude > 180.0) $tmpLongitude -= 360.0;
	            $tmpString = $this->encode($tmpLatitude, $tmpLongitude, $length);
	            if($tmpString == false) return false;
	            $arrayGeohash[] = $tmpString;
	        }
	    }
	    return $arrayGeohash;
	}
}

/**
 * LIB211 Geohash Exception
 * 
 * @author C!$C0^211
 *
 */
class LIB211GeohashException extends LIB211BaseException {
}