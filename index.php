<?php

/*
  Module: Index.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provided by the prado framework. Landing page activates the Application
 * when user accesses the page. and redirects the user to our home.php
 */

//$frameworkPath='\ChicoPantry\trunk\framework\prado.php';

$frameworkPath='framework/prado.php';
// The following directory checks may be removed if performance is required
$basePath=dirname(__FILE__);
$assetsPath=$basePath.'/assets';
$runtimePath=$basePath.'/protected/runtime';

if(!is_file($frameworkPath))
	die("Unable to find prado framework path $frameworkPath.");
if(!is_writable($assetsPath))
	die("Please make sure that the directory $assetsPath is writable by Web server process.");
if(!is_writable($runtimePath))
	die("Please make sure that the directory $runtimePath is writable by Web server process.");


require_once($frameworkPath);

$application=new TApplication;
$application->run();
?>