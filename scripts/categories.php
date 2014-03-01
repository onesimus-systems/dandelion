<?php

// TODO: Create a better method to check these URLs
// Because this file is called from the root and a subdirectory,
// a check needs to be done to determine who the required files are
// included.

require_once (is_file('scripts/dbconnect.php')) ? 'scripts/dbconnect.php' : '../scripts/dbconnect.php';
require_once (is_file('classes/categories.php')) ? 'classes/categories.php' : '../classes/categories.php';

// Authenticate user, if fail go to login page
if (!checkLogIn()) {
	header( 'Location: ../index.php' );
}

if(isset($_POST['parentID'])) {
	$displayCats = new Categories();
	
	$displayCats->getChildren($_POST['parentID']);
}