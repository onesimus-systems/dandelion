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
define('D_VERSION', '4.3.1');     	// Defines current Dandelion version
define('THEME_DIR', 'themes');	  	// Defines theme directory

// Load config into session variable
try {
	// Reduce this with root directory constant
	if (file_exists('config/config.php')) {
		include 'config/config.php';
		$_SESSION['config'] = $CONFIG;
	}
	elseif (file_exists('../config/config.php')) {
		include '../config/config.php';
		$_SESSION['config'] = $CONFIG;
	}
	else {
		throw new Exception('No configuration file found');
	}            
} catch(Exception $e) {
	echo 'Error: '.$e;
}

require_once 'themes.php';
require_once (is_file('classes/db_functions.php')) ? 'classes/db_functions.php' : '../classes/db_functions.php';
require_once 'authenticate.php';