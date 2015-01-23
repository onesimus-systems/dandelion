<?php
/**
  * This is the main entry point of Dandelion. All page and API requests are routed here.
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date December 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

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
if (is_file('pages/'.$mainCall.'.php') && Gatekeeper\authenticated()) {
    include 'pages/'.$mainCall.'.php';
} else {
    include 'pages/login.php';
}

session_write_close();
