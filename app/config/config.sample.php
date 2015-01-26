<?php
return array(
    // Database Configuration
    'db' => array(
        // Database type: mysql
        'db_type' => '',
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
    ),

    // Application configuration
    // FQDN/IP for the application
    'hostname' => 'http://localhost',
    // Name of PHP session for Dandelion, make unique for each instance of Dandelion
    'phpSessionName' => 'dan_session_1',
    // Debug mode => set to false in prod
    'debugEnabled' => false,
    // Is the app installed, set to false to rerun install script
    'installed' => false,
    // Application title displayed at top of pages
    'appTitle' => 'Dandelion Web Log',
    // Application tagline dispalyed below title
    'tagline' => 'Website Slogan',
    // Application default theme
    'defaultTheme' => 'Halloween',
    // If Cheesto status system is enabled
    'cheestoEnabled' => true,
    // If the public api is enabled
    'publicApiEnabled' => false
);
