<?php
/**
  * This file is a global file which is included on every page.
  * This script is used define any global aspects of Dandelion
  * and include other needed PHP scripts.
***/

// Remove these two lines during deployment
error_reporting(E_ALL);
ini_set('display_errors', True);

session_start();
$cookie_name = 'dandelionrememt'; // Used for login remembering (soon to go away)
define('D_VERSION', '4.3.1');     // Defines current Dandelion version
define('THEME_DIR', 'themes');	  // Defines theme directory

require_once ('miscLib.php');	  // Functions for login and theme name
require_once ('dbconnect.php');	  // Database functions