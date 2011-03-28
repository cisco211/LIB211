<?php

define('LIB211_EXEC',TRUE);

if (!defined('LIB211_DISALLOWCTPR')) define('LIB211_DISALLOWCTPR',TRUE); 

if (!defined('LIB211_OBCLEAN')) define('LIB211_OBCLEAN',TRUE); 

if (!defined('LIB211_AUTOLOAD')) define('LIB211_AUTOLOAD',TRUE);

if (!defined('LIB211_OPERATOR')) define('LIB211_OPERATOR',TRUE);

if (!defined('LIB211_TESTER')) define('LIB211_TESTER',FALSE);

if (!defined('LIB211_ROOT')) define("LIB211_ROOT",dirname(__FILE__));

if (!defined('EOL')) define('EOL',chr(13).chr(10));

if (LIB211_OBCLEAN === TRUE) @ob_clean();

if (LIB211_DISALLOWCTPR === TRUE) @ini_set("allow_call_time_pass_reference",0);

if (!file_exists(LIB211_ROOT.'/tmp')) {
	if (!mkdir(LIB211_ROOT.'/tmp')) throw new Exception('Could not create '.LIB211_ROOT.'/tmp');
	if (!file_exists(LIB211_ROOT.'/tmp/.lock') AND !mkdir(LIB211_ROOT.'/tmp/.lock')) throw new Exception('Could not create '.LIB211_ROOT.'/tmp/.lock');
}

require_once(LIB211_ROOT.'/core/Base.class.php');

if (LIB211_AUTOLOAD === TRUE) require_once(LIB211_ROOT.'/core/Autoload.class.php');

if (LIB211_OPERATOR === TRUE) require_once(LIB211_ROOT.'/core/Operator.class.php');

if (LIB211_TESTER === TRUE) require_once(LIB211_ROOT.'/core/Tester.class.php');