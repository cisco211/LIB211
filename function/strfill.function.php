<?php
/**
 * Fill string to a given size in any direction and any fill characters
 * @param string $string
 * @param integer $size
 * @param string $dir
 * @param string $chr
 * @return string
 */
function strfill($string,$size,$dir='l',$chr='0') {
	$len = strlen($string);
	for($i = $len; $i < $size; $i++) {
		if ($dir === 'r') $string = $string.$chr;
		else $string = $chr.$string;
	}
	return $string;
}