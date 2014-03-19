<?php
/**
  * This file is a global file which is included on every page.
  * This script is used define any global aspects of Dandelion
  * and include other needed PHP scripts.
  *
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', True);

session_start();

// Define constants
if (!defined('D_VERSION')) {
	define('D_VERSION', '4.3.1');     			// Defines current Dandelion version
}
if (!defined('THEME_DIR')) {
	define('THEME_DIR', 'themes');	 			// Defines theme directory
}
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(__FILE__)));	// Defines root path of application
}

// Load config into session variable
try {
	if (file_exists(ROOT . '/config/config.php')) {
		include ROOT.'/config/config.php';
		$_SESSION['config'] = $CONFIG;
	}
	else {
		throw new Exception('No configuration file found');
	}            
} catch(Exception $e) {
	echo 'Error: '.$e;
}

require_once ROOT.'/classes/db_functions.php';
require_once 'authenticate.php';
require_once 'userRights.php';
require_once 'themes.php';