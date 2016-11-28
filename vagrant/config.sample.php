<?php
/**
 * Configuration file for Vagrant box
 */
return array(
    'db' => array(
        'type' => 'mysql',
        'dbname' => 'dandelion',
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => 'a',
        'tablePrefix' => 'dan_',
    ),

    'hostname' => 'http://localhost:8081',
    'appTitle' => 'Dandelion Web Log',
    'tagline' => 'Website Slogan',
    'installed' => true,
    'debugEnabled' => true,
    'publicApiEnabled' => true
);
