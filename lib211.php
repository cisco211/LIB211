<?php

@ob_clean();

@ini_set("allow_call_time_pass_reference",0);

if (!defined('LIB211_MODERATOR')) define('LIB211_MODERATOR',TRUE);

if (!defined('LIB211_ROOT')) define("LIB211_ROOT",dirname(__FILE__));

if (!defined('EOL')) define('EOL',chr(13).chr(10));

define('LIB211_EXEC',TRUE);

require_once(LIB211_ROOT.'/core/Base.class.php');

require_once(LIB211_ROOT.'/core/Autoload.class.php');

if (defined('LIB211_MODERATOR') AND LIB211_MODERATOR === TRUE) require_once(LIB211_ROOT.'/core/Core.class.php');