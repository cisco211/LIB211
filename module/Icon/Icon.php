<?php
define("LIB211_IMPORT_ROOT",
 str_replace("/module/icon/0.100","",dirname(__FILE__)));
require(LIB211_IMPORT_ROOT."/lib211.php");
$LIB211 = new LIB211;
$LIB211->create("icon/0.100");
if (isset($_GET["in"])) $in = trim(htmlentities(urlencode($_GET["in"])));
else $in = "missing";
$LIB211->icon->loadpng(LIB211_ROOT."/module/icon/0.100/icon/".$in.".png");
if (isset($_GET["bg"])) $bg = hexdec(trim(htmlentities($_GET["bg"])));
else $bg = 0xFFFFFF;
$LIB211->icon->background($bg);
if (isset($_GET["fg"])) $fg = hexdec(trim(htmlentities($_GET["fg"])));
else $fg = 0x000000;
$LIB211->icon->foreground($fg);
if (isset($_GET["ty"])) $ty = trim(htmlentities($_GET["ty"]));
else $ty = "none";
$LIB211->icon->transparency($ty);
$cached = LIB211_ROOT."/module/icon/0.100/cache/".$in."_".$bg."_".$fg."_".$ty.".png";
header("content-type: image/png");
if (file_exists($cached)) {
 print file_get_contents($cached);
 }   //
else {
 $LIB211->icon->loadpng(LIB211_ROOT."/module/icon/0.100/icon/".$in.".png");
 $LIB211->icon->writepng($cached);
 print file_get_contents($cached);
 }   //
$LIB211->kill("icon");
exit();
?>