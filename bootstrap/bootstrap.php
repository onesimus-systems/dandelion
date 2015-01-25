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

// Get and define root path of application
define('BASE_DIR', dirname(dirname(__FILE__)));
define('ROOT', BASE_DIR.DIRECTORY_SEPARATOR.'app');

/**
 * Check running PHP version
 * The password compatibility library requires PHP version 5.3.7 or above
 */
if (!function_exists('version_compare') || version_compare(PHP_VERSION, '5.3.7', '<')) {
    require ROOT. '/lib/phpVersionError.php';
    PHPVersionError('site');
}

require ROOT . '/lib/interfaces.php';

// Setup error logging
require ROOT . '/lib/logging.php';
error_reporting(-1);
ini_set('log_errors', true);

// Load configuration
if (file_exists(ROOT . '/config/config.php')) {
    require ROOT . '/config/config.php';
}
else {
    trigger_error('No configuration file found. Please create ROOT/config/config.php.', E_USER_ERROR);
    echo 'No configuration file found.  Please create ROOT/config/config.php or <a href="./install">Start the Installer</a>.';
    exit(1);
}

// Define constants
define('D_VERSION', '6.0.0 dev');
define('DB_PREFIX', $DBCONFIG['db_prefix']);

// Display errors if in debug mode
if (DEBUG_ENABLED) {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}

// Give database class the info to connect
Storage\mySqlDatabase::$connInfo = $DBCONFIG;

// Check for and apply updates
require ROOT . '/lib/update.php';
update();

// Setup session manager
$timeout = 21600; // 6 hours
ini_set('session.gc_maxlifetime', $timeout);
require ROOT . '/lib/session_manager.php';
new SessionSaveHandler();
session_name(PHP_SESSION_NAME);
session_start();

// Load helper scripts
require ROOT . '/lib/helperScripts.php';
require ROOT . '/lib/themes.php';
require ROOT . '/lib/javascript.php';

// Load password comapatability library if version is less than 5.5
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require ROOT . '/lib/password-compat/password.php';
}

// Load application settings
if (!isset($_SESSION['app_settings'])) {
    $conn = Storage\mySqlDatabase::getInstance();
    $app_settings = $conn->selectAll('settings')->get();
    foreach ($app_settings as $setting) {
        $_SESSION['app_settings'][$setting['name']] = $setting['value'];
    }
    unset($conn);
}

// Load rights module for logged in user
if (Gatekeeper\authenticated()) {
    $User_Rights = new \Dandelion\rights($_SESSION['userInfo']['userid']);
}
