<?php
/**
  * This file is a global file which is included on every page.
  * This script is used to define any global aspects of Dandelion
  * and include other needed PHP scripts.
  *
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;
use Dandelion\Database\dbManage;

/**
 * This checks the time of the current session and if it has been
 * inactive for $timeout seconds long, destroy the session and start again
 */
$timeout = 21600; // 6 hours
ini_set('session.gc_maxlifetime', $timeout);
session_name('dan_session');
session_start();

/*
TODO: Work on caching
Header("Cache-Control: must-revalidate");
Cache-Control: max-age header

$offset = 60 * 60 * 24 * 3;
$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
Header($ExpStr);
*/

if (isset($_SESSION['timeout_idle']) && $_SESSION['timeout_idle'] < time()) {
    session_destroy();
    session_start();
    session_regenerate_id();
    $_SESSION = array();
}

$_SESSION['timeout_idle'] = time() + $timeout;

// Get and define root path of application
define('ROOT', dirname(dirname(__FILE__)));

// Load Composer dependencies
require_once ROOT.'/vendor/autoload.php';

// Setup error logging
require_once ROOT.'/scripts/logging.php';
error_reporting(-1);
ini_set('log_errors', true);

// Utilized in development to, well, force the config to reload without relogin.
$forceConfigLoad = true;

// Load config into session variable
if(!isset($_SESSION['config']) || $forceConfigLoad) {
    if (file_exists(ROOT . '/config/config.php')) {
        include ROOT.'/config/config.php';

        if (isset($CONFIG)) {
            $_SESSION['config'] = $CONFIG;
        } else {
            trigger_error('The configuration file is corrupt or otherwise damaged. Please check config.php and try again.', E_USER_ERROR);
            echo 'The configuration file is corrupt or otherwise damaged. Please check config.php and try again.';
            exit(1);
        }
    } else {
        trigger_error('No configuration file found. Please create ROOT/config/config.php.', E_USER_ERROR);
        echo 'No configuration file found.  Please create ROOT/config/config.php or <a href="./install">Start the Installer</a>.';
        exit(1);
    }
}

// Display errors if in debug mode
if ($_SESSION['config']['debug']) {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}

/**
 * Check running PHP version
 * The password compatibility library requires PHP version 5.3.7 or above
 */
if (!function_exists( 'version_compare' ) || version_compare(phpversion(), "5.3.7", "<")) {
    require 'phpVersionError.php';
    PHPVersionError( 'site' );
}

// Define constants
define('DB_PREFIX', $_SESSION['config']['db_prefix']);
define('HOSTNAME', $_SESSION['config']['hostname']);
define('D_VERSION', '5.0.0');
define('THEME_DIR', 'themes');

// Load helper scripts
require_once ROOT.'/scripts/autoloader.php';
require_once ROOT.'/scripts/authenticate.php';
require_once ROOT.'/scripts/themes.php';
require_once ROOT.'/scripts/scripts.php';

// Load application settings
if(!isset($_SESSION['app_settings'])) {
    $conn = new dbManage();
    $app_settings = $conn->selectAll('settings');
    foreach($app_settings as $setting) {
        $_SESSION['app_settings'][$setting['name']] = $setting['value'];
    }
    unset($conn);
}

// Define request source, used for API check
$req_source = '';
