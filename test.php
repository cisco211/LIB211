#!/usr/bin/php
<?php

// Debugging
@ini_set('display_errors','on'); @error_reporting(E_ALL);

// Time limit
set_time_limit(0);

// Timezone (must be set)
date_default_timezone_set('Europe/Berlin');

// Preconfigure LIB211
define('LIB211_AUTOLOAD',FALSE);
define('LIB211_OPERATOR',FALSE);
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

Usage: test.php [-h] [-t <testName>|<testName>,<testName>,...|all]
-t = Run test <testName(s)> or run "all" tests
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
			
			// Run tests separated by , 
			elseif (preg_match('/,/',$test) === 1) {
				$tests = explode(',',$test);
				$tester->setTests($tests);
				try {
					$tester->runTests();
				}
				catch (LIB211TesterException $e) {
					exit(EOL.$e->getMessage().EOL.EOL);
				}
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
				print $tester->getResultsAsText($test);
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

		// HTML View not finished
		#$_GET['cli'] = '';
		
		if (isset($_GET['cli'])) {
			header('content-type: text/plain');
			@chdir(LIB211_ROOT);
			ob_start();
			if (isset($_GET['test'])) $test = (string)$_GET['test'];
			else $test = '';
			if (empty($test)) {
				print <<<ENDHLP
LIB211Tester

Usage: test.php?cli&test=<testName>|<testName>,<testName>,...|all
 cli = Run as shell command
 test = Run test <testName(s)> or run "all" tests

ENDHLP;
			} else {
				passthru(escapeshellcmd('./test.php -t '.$test));
			}
			$output = ob_get_contents();
			ob_clean();
			print $output;
		} else {
			
			// Create testrunner
			$tester = new LIB211Tester();
			
			$alltests = $tester->getTests();
			
			// Get test name
			$test = (string)trim(htmlentities(@$_GET['test']));
			
			if (empty($test)) {
				$result = 'No tests given';
			}
			
			// Run all tests
			elseif (strtolower($test) == 'all') {
				try {
					$tester->runTests();
					$result = $tester->getResultsAsHTML();#.$tester->getResultsAsText();
				}
				catch (LIB211TesterException $e) {
					$result = $e->getMessage();
				}
			}
			
			// Run tests separated by , 
			elseif (preg_match('/,/',$test) === 1) {
				$tests = explode(',',$test);
				$tester->setTests($tests);
				try {
					$tester->runTests();
					$result = $tester->getResultsAsHTML();#.$tester->getResultsAsText();
				}
				catch (LIB211TesterException $e) {
					$result = $e->getMessage();
				}
				
			}
			
			// Run a test
			else {
				try {
					$tester->runTest($test);
					$result = $tester->getResultsAsHTML($test);#.$tester->getResultsAsText($test);
				}
				catch (LIB211TesterException $e) {
					$result = $e->getMessage();
				}
				
			}
			
			// Kill testrunner
			unset($tester);
			
			$testsAvailable = '';
			
			foreach ($alltests as $entry) {
				$testsAvailable .= '<option value="'.$entry['name'].'">'.$entry['name'].'</option>';
			}
			
			$runlist = explode(',',$test);
			$testsToRun = '';
			foreach ($runlist as $entry) {
				if (!empty($entry) AND $entry != 'all') $testsToRun .= '<option value="'.$entry.'">'.$entry.'</option>';
				
			}
			$ignore = array('.','..','.cvs','.git','.svn','.DS_Store');
			$list = scandir(LIB211_ROOT.'/module');
			$moduleLinks = '';
			foreach ($list as $module) {
				if (!in_array($module,$ignore) AND file_exists(LIB211_ROOT.'/module/'.$module.'/'.$module.'.class.php')) {
					$moduleLinks .= '<a href="./doc/LIB211/LIB211'.$module.'.html" target="_blank">LIB211'.$module.'</a><br/>';
				}
			}
			$testLinks = '';
			foreach ($alltests as $entry) {
				$testLinks .= '<a href="./test.php?test='.$entry['name'].'">Run '.$entry['name'].'</a><br/>';
			}
			// Print HTML output
			exit(<<<ENDHTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>LIB211 Testrunner</title>
		<link rel="stylesheet" type="text/css" href="./doc/media/style.css"/>
		<script type="text/javascript">
			/*<![CDATA[*/
			var NS4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);

			function addOption(selection,text,value) {
  				var option = new Option(text,value);
  				var selectedLength = selection.length;
  				selection.options[selectedLength] = option;
			}

			function deleteOption(selection,index) { 
  				var selectedLength = selection.length;
  				if(selectedLength>0) {
    				selection.options[index] = null;
  				}
			}

			function copyAllOptions(selTestsAvailable,selTestsToRun) {
				var selectedLength = selTestsAvailable.length;
  				var selectedText = new Array();
  				var selectedValues = new Array();
  				var selectedCount = 0;
 				var i;
  				for(i=selectedLength-1; i>=0; i--) {
      				selectedText[selectedCount] = selTestsAvailable.options[i].text;
      				selectedValues[selectedCount] = selTestsAvailable.options[i].value;
      				selectedCount++;
  				}
  				for(i=selectedCount-1; i>=0; i--) {
    				addOption(selTestsToRun, selectedText[i], selectedValues[i]);
  				}
  				if(NS4) history.go(0);
			}
			
			function copyOptions(selTestsAvailable,selTestsToRun) {
				var selectedLength = selTestsAvailable.length;
  				var selectedText = new Array();
  				var selectedValues = new Array();
  				var selectedCount = 0;
 				var i;
  				for(i=selectedLength-1; i>=0; i--) {
    				if(selTestsAvailable.options[i].selected) {
      					selectedText[selectedCount] = selTestsAvailable.options[i].text;
      					selectedValues[selectedCount] = selTestsAvailable.options[i].value;
      					selectedCount++;
    				}
  				}
  				for(i=selectedCount-1; i>=0; i--) {
    				addOption(selTestsToRun, selectedText[i], selectedValues[i]);
  				}
  				if(NS4) history.go(0);
			}
			
			function deleteAllOptions(formular) {
				formular.form.testsToRun.innerHTML=null;
			}
			
			function deleteOptions(selTestsAvailable) {
				var selectedLength = selTestsAvailable.length;
  				var selectedText = new Array();
  				var selectedValues = new Array();
  				var selectedCount = 0;
 				var i;
  				for(i=selectedLength-1; i>=0; i--) {
    				if(selTestsAvailable.options[i].selected) {
      					selectedText[selectedCount] = selTestsAvailable.options[i].text;
      					selectedValues[selectedCount] = selTestsAvailable.options[i].value;
      					deleteOption(selTestsAvailable, i);
      					selectedCount++;
    				}
  				}
  				if(NS4) history.go(0);
			}
			
			function sel2string(selection) {
				var selectionLength = selection.length;
				var i;
				var string = '';
				for (i=0; i < selectionLength; i++) {
					string += selection.options[i].value;
					if (i != selectionLength-1) string += ','; 
				}
				return string;
			}
			function sel2url(selection) {
				return 'http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?test='+sel2string(selection);
			}
			function updateForm(formular) {
				formular.form.test.value=sel2string(formular.form.testsToRun);
				formular.form.url.value=sel2url(formular.form.testsToRun);
				var link = document.getElementById('link');
				link.href=sel2url(formular.form.testsToRun);
			}
			function submitForm(formular) {
				document.location.href='test.php?test='+sel2string(formular.form.testsToRun);
			}
			/*]]>*/
		</script>
	</head>
	<body>
		<table border="0" cellspacing="0" cellpadding="0" style="height:40px;" width="100%">
			<tr>
				<td class="header_top">LIB211</td>
			</tr>
			<tr>
				<td class="header_line"><img src="./doc/media/empty.png" width="1" height="1" border="0" alt=""  /></td>
			</tr>
			<tr>
				<td class="header_menu">
					[ <a class="menu" href="https://github.com/tschumacher/LIB211" target="blank">github: LIB211</a> ]
					[ <a class="menu" href="http://php.net" target="blank">PHP</a> ]
				</td>
			</tr>
			<tr>
				<td class="header_line"><img src="./doc/media/empty.png" width="1" height="1" border="0" alt=""  /></td>
			</tr>
		</table>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr valign="top">
				<td width="200" class="menu">
					<div id="ric">
						<p><a href="./doc/ric_CHANGELOG.html" target="_blank">CHANGELOG</a></p>
						<p><a href="./doc/ric_FAQ.html" target="_blank">FAQ</a></p>
						<p><a href="./doc/ric_INSTALL.html" target="_blank">INSTALL</a></p>
						<p><a href="./doc/ric_README.html" target="_blank">README</a></p>
						</div>
					<b><a href='./doc/index.html' target="_blank">Documentation:</a></b><br/>
					<div class="package">
						<a href="./doc/LIB211/LIB211.html" target="_blank">LIB211</a><br/>
						<a href="./doc/LIB211/LIB211Autoload.html" target="_blank">LIB211Autoload</a><br/>
						<a href="./doc/LIB211/LIB211Base.html" target="_blank">LIB211Base</a><br/>
						{$moduleLinks}
						<a href="./doc/LIB211/LIB211Tester.html" target="_blank">LIB211Tester</a><br/>
						<a href="./doc/LIB211/LIB211Testclass.html" target="_blank">LIB211Testclass</a>
					</div>
					<br/>
					<b><a href='./test.php'>Testrunner:</a></b>
					<div class="package">
						<a href="./test.php?test=all">Run all tests</a><br/>
						<a href="./test.php?cli&amp;test=all" target="_blank">Run with CLI</a><br/>
						{$testLinks}
					</div>
					<br/>
					<a href="./index.php">&lt;&lt;&lt; Back to overview</a>
				</td>
				<td>
					<table cellpadding="10" cellspacing="0" width="100%" border="0">
						<tr>
							<td valign="top">
								<div align="center"><h1>LIB211 Testrunner</h1></div>
								<div>
									<b>Test Plan</b>
									<form action="#" method="get">
										<table style="width:100%;">
											<tr>
												<td style="width:50%;">
													<label for="testsAvailable">Tests available</label>
													<select name="testsAvailable" size="10" multiple="multiple" style="width:100%;" title="The list of all available tests">
														{$testsAvailable}
													</select>
												</td>
												<td align="center" valign="middle">
													<input type="button" value="+" title="Copy selection in 'Tests available' to 'Tests to run'" onclick="javascript:copyOptions(this.form.testsAvailable,this.form.testsToRun);updateForm(this);"/>
													<input type="button" value="++" title="Copy all in 'Tests available' to 'Tests to run'" onclick="javascript:copyAllOptions(this.form.testsAvailable,this.form.testsToRun);updateForm(this);"/>
												</td>
												<td style="width:50%;">
													<label for="testsToRun">Tests to run</label>
													<select name="testsToRun" size="10" multiple="multiple" style="width:100%;" title="Tests listed in this box will be executed">
														{$testsToRun}
													</select>
												</td>
												<td>
													<input type="button" value="-" title="Delete selection in 'Tests to run'" onclick="javascript:deleteOptions(this.form.testsToRun);updateForm(this);"/>
													<input type="button" value="--" title="Delete all in 'Tests to run'" onclick="javascript:deleteAllOptions(this);updateForm(this);"/>
												</td>
											</tr>
										</table>
										Tests:&nbsp;<input type="text" name="test" value="{$test}" style="width:95%;" readonly="readonly" onclick="javascript:this.select();"/><br/>
										URL:&nbsp;<input type="text" name="url" value="http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?test={$test}" style="width:94%;" readonly="readonly" onclick="javascript:this.select();"/>
										<a id="link" href="http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?test={$test}" target="_blank">Link</a><br/><br/>
										<center>
											<input type="button" title="Run all available tests once" value="Run all tests" onclick="javascript:document.location.href='test.php?test=all';"/>
											<input type="button" title="Run the test(s) you have added to 'Tests to run'" value="Run selected tests" onclick="javascript:submitForm(this);"/>
											<input type="button" title="Use LIB211Tester in a wrapped CLI mode" value="Run with CLI" onclick="javascript:document.location.href='test.php?cli&amp;test='+this.form.test.value;"/>
											<input type="button" title="Clear settings and results" value="Clear" onclick="javascript:document.location.href='test.php';"/>
											<input type="button" title="Back to root" value="Back" onclick="javascript:document.location.href='index.php';"/>
										</center>
									</form>
								</div>
								
								<div>
									<b>Test Results</b><br/><br/>
									<!--<div style="font-family:monospace,'courier new';overflow:auto;white-space:pre-wrap;width:auto;">{$result}</div>-->
									{$result}
								</div>
								<div class="credit">
									<hr/>
									LIB211 &copy; 2007 - 2011 by <a href="http://cisco211.de" target="_blank">C!\$C0^211</a>, Documentation generated by <a href="http://www.phpdoc.org" target="_blank">phpDocumentor</a> 
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
ENDHTML
			);
		}
		
	break;

}

// Final exit
exit();