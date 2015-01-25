<?php
// Database Configuration
$DBCONFIG=array(
    // Database type: mysql or sqlite
    'db_type' => '',
    // Name of sqlite database file
    'sqlite_fn' => '',
    // Database name of mysql
    'db_name' => '',
    // Database hostname/IP for mysql
    'db_host' => '',
    // User for mysql
    'db_user' => '',
    // Password for above user
    'db_pass' => '',
    // Database table prefix
    'db_prefix' => 'dan_',
);

// Application configuration
// FQDN/IP for the application
define('HOSTNAME', '');
// Name of PHP session for Dandelion, make unique for each instance of Dandelion
define('PHP_SESSION_NAME', 'dan_session_1');
// Debug mode, set to false in prod
define('DEBUG_ENABLED', false);
// Is the app installed, set to false to rerun install script
define('INSTALLED', true);
// Directory of theme files
define('THEME_DIR', 'assets/themes');
// Directory of public files
define('PUBLIC_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'public');
