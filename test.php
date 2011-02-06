#!/usr/bin/php
<?php

// Debugging
@ini_set('display_errors','on'); @error_reporting(E_ALL);

// Timezone (must be set)
date_default_timezone_set('Europe/Berlin');

// Preconfigure LIB211
define('LIB211_OPERATOR',TRUE);
define('LIB211_TESTER',TRUE);

// Load LIB211
require('lib211.php');

// Select output platform
switch (php_sapi_name()) {
	
	// Run as shell script
	case 'cli':
		
		print 'LIB211Tester'.EOL;
		
		// Filter arguments
		$opt_short = '';
		$opt_short .= 'h';
		$opt_short .= 't:';
		$options = getopt($opt_short);
		
		// Show help
		if (empty($options) OR isset($options['h'])) {
			print <<<ENDHLP

Usage: test.php [-h] [-t <testName>|all]
-t = Run test <testName> or run "all" tests
-h = Show this Help


ENDHLP;
			exit();
		}
		
		// Run test(s)
		if (isset($options['t']) AND !empty($options['t'])) {
			
			// Create testrunner
			$tester = new LIB211Tester();
			
			// Get test name
			$test = (string)$options['t'];
			
			// Run all tests
			if (strtolower($test) == 'all') {
				$tester->runTests();
				print $tester->getResultsAsText();
			}
			
			// Run a test
			else {
				try {
					$tester->runTest($test);
				}
				catch (LIB211TesterException $e) {
					exit(EOL.$e->getMessage().EOL.EOL);
				}
				print $tester->getResultAsText($test);
			}
			
			// Kill testrunner
			unset($tester);
			
		}
		
	break;

	// Run all others as webpage (aolserver, apache, apache2filter, apache2handler, caudium, cgi (until PHP 5.3), cgi-fcgi,
	//                            continuity, embed, isapi, litespeed, milter, nsapi, phttpd, pi3web, roxen, thttpd, tux and webjames)
	default:
		
		// We dont want the 'shebang' in output
		@ob_clean();
		
		// Lets do a fast and cool solution:
		header('content-type: text/plain');
		@chdir(LIB211_ROOT);
		ob_start();
		$query = (string)$_SERVER['QUERY_STRING'];
		if (empty($query)) passthru('./test.php -t all');
		else {
			$tester = new LIB211Tester();
			$list = $tester->getTests();
			foreach ($list as $test) {
				if ($test['name'] == $query AND
				    preg_match('/^[a-zA-Z]+[a-zA-z0-9\_]*$/',$query)) {
					passthru('./test.php -t '.$query);
				}
			}
		}
		$output = ob_get_contents();
		ob_clean();
		print $output;
		
	break;

}

// Final exit
exit();