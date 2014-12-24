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
use Dandelion\Storage\mySqlDatabase;

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

// Include autoloaders
require ROOT . '/lib/autoloader.php';  // Dandelion
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
define('THEME_DIR', 'themes');
define('DB_PREFIX', $DBCONFIG['db_prefix']);
define('FAVICON_PATH', HOSTNAME.'/static/images/favicon.ico');

// Display errors if in debug mode
if (DEBUG_ENABLED) {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}

// Give database class the info to connect
dbManage::$connInfo = $DBCONFIG;
mySqlDatabase::$connInfo = $DBCONFIG;

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
require_once ROOT . '/lib/authenticate.php';
require ROOT . '/lib/themes.php';
require ROOT . '/lib/javascript.php';
require ROOT . '/lib/password-compat/password.php';
require ROOT . '/lib/helperScripts.php';

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
