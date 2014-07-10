<?php
$CONFIG = array(
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
    // Name of PHP session for Dandelion, make unique for each instance of Dandelion
    'session_name' => 'dan_session_1',
    // Is the app installed, set to false to rerun install script
    'installed' => true,
    // Debug mode, set to false in prod
    'debug' => false,
    // FQDN/IP for the application
    'hostname' => ''
);
