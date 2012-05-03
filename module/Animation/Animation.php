<?php
/**
 * @package LIB211
 */

/**
 * Animation default api
 */
define('LIB211_IMPORT_ROOT',str_replace('/module/Animation','',dirname(__FILE__)));
require_once(LIB211_IMPORT_ROOT.'/lib211.php');
require_once(LIB211_IMPORT_ROOT.'/module/Animation/Animation.class.php');
$animation = new LIB211Animation();
if (isset($_GET['in'])) $in = trim(htmlentities(urlencode($_GET['in'])));
else $in = 'test';
if (isset($_GET['bg'])) $bg = hexdec(trim(htmlentities($_GET['bg'])));
else $bg = 0xFFFFFF;
$animation->background($bg);
if (isset($_GET['fg'])) $fg = hexdec(trim(htmlentities($_GET['fg'])));
else $fg = 0x000000;
$animation->foreground($fg);
if (isset($_GET['ty'])) $ty = trim(htmlentities($_GET['ty']));
else $ty = 'none';
$animation->transparency($ty);
$cached = LIB211_ROOT.'/module/Animation/cache/'.$in.'_'.$bg.'_'.$fg.'_'.$ty.'.gif';
header('content-type: image/gif');
if (file_exists($cached)) {
 print file_get_contents($cached);
 }
else {
 $animation->load(LIB211_ROOT.'/module/Animation/animation/'.$in);
 $animation->write($cached);
 print file_get_contents($cached);
 }
exit();