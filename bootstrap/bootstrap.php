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

use \Dandelion\Logging;
use \Dandelion\Utils\Updater;
use \Dandelion\Storage\MySqlDatabase;
use \Dandelion\Session\SessionManager;

// Get and define root path of application
define('D_VERSION', '6.0.0-dev');
define('BASE_DIR', dirname(dirname(__FILE__)));
define('ROOT', BASE_DIR.DIRECTORY_SEPARATOR.'app');

// Check PHP version, Dandelion supports only PHP 5.4 and above
if (!function_exists('version_compare') || version_compare(PHP_VERSION, '5.4.0', '<')) {
    require ROOT. '/lib/phpVersionError.php';
    PHPVersionError('site');
}

// Load password comapatability library if version is less than 5.5
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require ROOT . '/lib/password-compat/password.php';
}

require ROOT . '/lib/interfaces.php';

// Load configuration
if (file_exists(ROOT . '/config/config.php')) {
    require ROOT . '/config/config.php';
}
else {
    trigger_error('No configuration file found. Please create ROOT/config/config.php.', E_USER_ERROR);
    echo 'No configuration file found.  Please create ROOT/config/config.php or <a href="./install">Start the Installer</a>.';
    exit(1);
}

// Register logging system
Logging::register();

// Define constants
define('DB_PREFIX', $DBCONFIG['db_prefix']);

// Give database class the info to connect
MySqlDatabase::$connInfo = $DBCONFIG;
MySqlDatabase::$dbPrefix = $DBCONFIG['db_prefix'];

// Check for and apply updates
Updater::checkForUpdate();

// Setup session manager
SessionManager::register();
SessionManager::startSession();

// Load helper scripts
require ROOT . '/lib/helperScripts.php';
require ROOT . '/lib/themes.php';
require ROOT . '/lib/javascript.php';

// Load application settings
if (!isset($_SESSION['app_settings'])) {
    $conn = MySqlDatabase::getInstance();
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
