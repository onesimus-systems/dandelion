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
session_start();

// Define constants
if (!defined('D_VERSION')) {
	define('D_VERSION', '4.4.0');     			// Defines current Dandelion version
}
if (!defined('THEME_DIR')) {
	define('THEME_DIR', 'themes');	 			// Defines theme directory
}
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(__FILE__)));	// Defines root path of application
}

// Load other scripts
require_once ROOT.'/classes/db_functions.php';
require_once ROOT.'/scripts/authenticate.php';
require_once ROOT.'/scripts/userRights.php';
require_once ROOT.'/scripts/themes.php';
require_once ROOT.'/scripts/logging.php';

// Load config into session variable
if(!isset($_SESSION['config'])) {
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
}

if (!defined('DB_PREFIX')) {
	define('DB_PREFIX', $_SESSION['config']['db_prefix']);	// DB table prefix as a constant
}

// Set error reporting if in debug mode
if ($_SESSION['config']['debug']) {
	error_reporting(E_ALL);
	ini_set('display_errors', True);
}

// Load application settings
if(!isset($_SESSION['app_settings'])) {
	$conn = new dbManage();
	$app_settings = $conn->selectAll('settings');
	foreach($app_settings as $setting) {
		$_SESSION['app_settings'][$setting['name']] = $setting['value'];
	}
	unset($conn);
}