<?php
/**
 * This file is a global file which is included on every page.
 * This script is used to define any global aspects of Dandelion
 * and include other needed PHP scripts.
 *
 * @author Lee Keitel
 *         @date March 2014
 *        
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 * 
 */
namespace Dandelion;

use Dandelion\Database\dbManage;

// Get and define root path of application
define('ROOT', dirname(dirname(__FILE__)));

/**
 * Check running PHP version
 * The password compatibility library requires PHP version 5.3.7 or above
 */
if (!function_exists('version_compare') || version_compare(phpversion(), "5.3.7", "<")) {
    require ROOT. '/lib/phpVersionError.php';
    PHPVersionError('site');
}

// Load Composer dependencies
require_once ROOT . '/vendor/autoload.php';
require_once ROOT . '/lib/autoloader.php';

// Setup error logging
require_once ROOT . '/lib/logging.php';
error_reporting(-1);
ini_set('log_errors', true);

// Load configuration
if (file_exists(ROOT . '/config/config.php')) {
    require ROOT . '/config/config.php';
    
    if (!isset($CONFIG)) {
        trigger_error('The configuration file is corrupt or otherwise damaged. Please check config.php and try again.', E_USER_ERROR);
        echo 'The configuration file is corrupt or otherwise damaged. Please check config.php and try again.';
        exit(1);
    }
}
else {
    trigger_error('No configuration file found. Please create ROOT/config/config.php.', E_USER_ERROR);
    echo 'No configuration file found.  Please create ROOT/config/config.php or <a href="./install">Start the Installer</a>.';
    exit(1);
}

// Define constants
define('DB_PREFIX', $CONFIG['db_prefix']);
define('HOSTNAME', $CONFIG['hostname']);
define('DEBUG_ENABLED', $CONFIG['debug']);
define('INSTALLED', $CONFIG['installed']);
define('D_VERSION', '5.0.0');
define('THEME_DIR', 'themes');

// Display errors if in debug mode
if (DEBUG_ENABLED) {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}

// Give database class the info to connect
dbManage::$connInfo = $CONFIG;

/**
 * This checks the time of the current session and if it has been
 * inactive for $timeout seconds long, destroy the session and start again
 */
$timeout = 21600; // 6 hours
ini_set('session.gc_maxlifetime', $timeout);

require ROOT . '/lib/session_manager.php';
session_name($CONFIG['session_name']);
session_start();
/*
if (isset($_SESSION['timeout_idle']) && $_SESSION['timeout_idle'] < time()) {
    session_destroy();
    session_start();
    session_regenerate_id();
    $_SESSION = array();
}

$_SESSION['timeout_idle'] = time() + $timeout;*/

// Load helper scripts
require_once ROOT . '/lib/authenticate.php';
require_once ROOT . '/lib/themes.php';
require_once ROOT . '/lib/scripts.php';

// Load application settings
if (!isset($_SESSION['app_settings'])) {
    $conn = new dbManage();
    $app_settings = $conn->selectAll('settings');
    foreach ($app_settings as $setting) {
        $_SESSION['app_settings'][$setting['name']] = $setting['value'];
    }
    unset($conn);
}

// Load rights module for logged in user
if (Gatekeeper\authenticated()) {
    $User_Rights = new \Dandelion\rights($_SESSION['userInfo']['userid']);
}
