<?php
/**
 * Configuration file for Vagrant box
 */

$config['db']['type'] = 'mysql';
$config['db']['dbname'] = 'dandelion';
$config['db']['hostname'] = 'dandy-db';
$config['db']['username'] = 'root';
$config['db']['password'] = 'dandy_dev';
$config['db']['tablePrefix'] = 'dan_';

$config['hostname'] = 'http://localhost:8081';
$config['appTitle'] = 'Dandelion Web Log';
$config['tagline'] = 'Website Slogan';
$config['installed'] = true;
$config['debugEnabled'] = true;
$config['publicApiEnabled'] = true;

$config['jwtSecret'] = 'l5KZ33Be9wzzVuWVMlqn';
