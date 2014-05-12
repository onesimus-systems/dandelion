<?php
/**
 * Controller for category management
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date March 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

// TODO: Create a better method to check these URLs
// Because this file is called from the root and a subdirectory,
// a check needs to be done to determine how the required files are
// included.

require_once (is_file('scripts/grabber.php')) ? 'scripts/grabber.php' : '../scripts/grabber.php';
require_once (is_file('classes/categories.php')) ? 'classes/categories.php' : '../classes/categories.php';

// Authenticate user, if fail go to login page
if (!authenticated()) {
	header( 'Location: ../index.php' );
}

if (isset($_POST['action'])) {
	if($_POST['action'] == 'grabcats') {
		$past = json_decode(stripslashes($_POST['pastSelections']));
		$displayCats = new Categories();	
		$displayCats->getChildren($_POST['parentID'], $past);
	}
	
	elseif($_POST['action'] == 'addcat') {
	    if ($_SESSION['rights']['addcat']) {
    		$parent = $_POST['parentID'];
    		$desc = $_POST['catDesc'];
    		
    		$createCat = new Categories();
    		$createCat->addCategory($parent, $desc);
	    }
	    
	    else {
	        echo 'Your account doesn\'t have permissions to add a category.';
	    }
	}
	
	elseif($_POST['action'] == 'delcat') {
	    if ($_SESSION['rights']['deletecat']) {
    		$cat = $_POST['cid'];
    		
    		$deleteCat = new Categories();
    		$deleteCat->delCategory($cat);
	    }
	    
	    else {
	        echo 'Your account doesn\'t have permissions to delete a category.';
	    }
	}
	
	elseif($_POST['action'] == 'editcat') {
	    if ($_SESSION['rights']['editcat']) {
    		$cid = $_POST['cid'];
    		$desc = $_POST['catDesc'];
    		
    		$editCat = new Categories();
    		$editCat->editCategory($cid, $desc);
	    }
	    
	    else {
	        echo 'Your account doesn\'t have permissions to edit a category.';
	    }
	}
}