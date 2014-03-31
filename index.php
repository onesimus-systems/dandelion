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

include_once 'scripts/grabber.php';

if (!$_SESSION['config']['installed']) {
	header( 'Location: install.php' );
}

if (authenticated()) {
    include 'viewlog.phtml';
}

else {
    $showlogin = true;
    $status = isset($_SESSION['badlogin']) ? $_SESSION['badlogin'] : '&nbsp;';
	
	$theme = getTheme();
	include 'loginbox.php';
}