<?php
/**
  * This is the homepage of Dandelion.
  * If a user is already logged in it will
  * redirect them to the viewlog page.
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

/*! \mainpage Dandelion Weblog
 *
 *  \section intro_sec Introduction
 *
 * Dandelion was conceived after thinking of a way to replace the aging and slow Bloxom-based
 * log we were currently using to document change logs. I wanted to keep the web-based system,
 * use an SQL database instead of text files, and make every action possible via the browser
 * instead of having to SSH into the Blosxom server.
 *
 * And that is how Dandelion was born.
 */
require_once 'lib/bootstrap.php';

// Load installer if necassary
if (!INSTALLED) {
    redirect('installer');
}

// Process url
$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
$url = explode('/', $url);

// Get the first element which is the main page or command
$mainCall = array_shift($url);

// Call the API if it's an api call
if ($mainCall == "api") {
  include 'api/index.php';
  header('Content-Type: application/json');
  API\processCall($url);
  session_write_close();
  exit(0);
}

// Set the homepage if necassary
if ($mainCall == "") {
  // Homepage
  $mainCall = "viewlog";
}

// Load page
$indexCall = true;
if (Gatekeeper\authenticated()) {
    include 'pages/'.$mainCall.'.php';
} else {
    include 'pages/login.php';
}

session_write_close();
