<?php
return array(
    // Database Configuration
    'db' => array(
        // Database type: mysql, sqlite
        'type' => '',
        // Database name of mysql
        'dbname' => '',
        // Database hostname/IP for mysql
        // Database filename for sqlite
        'hostname' => '',
        // User for mysql
        'username' => '',
        // Password for above user
        'password' => '',
        // Database table prefix
        'tablePrefix' => 'dan_',
    ),

    'cheesto' => array(
        // Pre-made options for statuses
        'statusOptions' => array(
            'Available',
            'Away From Desk',
            'At Lunch',
            'Out for Day',
            'Out',
            'Appointment',
            'Do Not Disturb',
            'Meeting',
            'Out Sick',
            'Vacation'
        )
    ),

    // Application configuration
    // FQDN/IP for the application
    'hostname' => 'http://localhost',
    // Prefix for cookies managed by Dandelion
    'cookiePrefix' => 'dan_',
    // Name of PHP session for Dandelion, make unique for each instance of Dandelion
    'phpSessionName' => 'session_1',
    // Garbage collection lottery, the odds that a GC run will happen on a session open
    // Default is 1 out of 100.
    'gcLottery' => [1, 100],
    // Session timeout in minutes. Default is 6 hours
    'sessionTimeout' => 360,
    // Debug mode => set to false in prod
    'debugEnabled' => false,
    // Is the app installed, set to false to rerun install script
    'installed' => false,
    // Application title displayed at top of pages
    'appTitle' => 'Dandelion Web Log',
    // Application tagline displayed below title
    'tagline' => 'Website Slogan',
    // Application default theme
    'defaultTheme' => 'modern',
    // Cheesto status system is enabled
    'cheestoEnabled' => true,
    // If the public api is enabled
    'publicApiEnabled' => false,
    // Have Dandelion check online for a new version
    'checkForUpdates' => true
);
