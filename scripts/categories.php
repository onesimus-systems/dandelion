<?php

/// TODO: Create a better method to check these URLs
require_once (is_file('scripts/dbconnect.php')) ? 'scripts/dbconnect.php' : 'dbconnect.php';
require_once (is_file('classes/categories.php')) ? 'classes/categories.php' : '../classes/categories.php';

// Authenticate user, if fail go to login page
if (!checkLogIn()) {
	header( 'Location: ../index.php' );
}

if (empty($_POST['action'])) {
	$displayCats = new Categories();
	$displayCats->showAllCats();
}
elseif ($_POST['action'] == 'delete') {
	$deleteCat = new Categories(false);     // create category instance
	$depth = explode(":", $_POST['item']);  // create array from category tree
	$empty = array_shift($depth); 			// remove last element of array
	print_r($depth);
}
