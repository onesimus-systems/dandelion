<?php
/*
 * Lee Keitel
 * January 28, 2014
 *
 * This script is called via AJAX to create a new log entry.
 * 
*/

include 'dbconnect.php';

if (checkLogIn()) {
	if ($_SESSION['userInfo']['role'] == "guest") {
		header( 'Location: viewlog.phtml' );
	}
	
	if ($_SESSION['userInfo']['role'] === "admin") {
		$admin_link = '| <a href="admin.phtml">Administration</a>';
	}
	else {
		$admin_link = '';
	}
	
	if ($_SESSION['userInfo']['role'] !== "guest") {
		$settings_link = '| <a href="settings.phtml">Settings</a>';
	}
	else {
		$settings_link = '';
	}
}
else {
	header( 'Location: index.php' );
}

if ($_SESSION['userInfo']['username'] != 'ajmartin') { // This is the skeleton of an eventual blacklist/rights management
    // Grab all the variables from the POST array
    $new_title = isset($_POST['add_title']) ? $_POST['add_title'] : '';
    $new_entry = isset($_POST['add_entry']) ? $_POST['add_entry'] : '';
    $new_category_1 = isset($_POST['cat_1']) ? $_POST['cat_1'] : '';
    $new_category_2 = isset($_POST['cat_2']) ? $_POST['cat_2'] : '';
    $new_category_3 = isset($_POST['cat_3']) ? $_POST['cat_3'] : '';
    $new_category_4 = isset($_POST['cat_4']) ? $_POST['cat_4'] : '';
    $new_category_5 = isset($_POST['cat_5']) ? $_POST['cat_5'] : '';

    $new_cat = $new_category_1;
    $cat_stop = 0;

    // Check that all required fields have been entered
    if ($new_title != NULL AND $new_title != "" AND $new_entry != NULL AND $new_entry != "" AND $new_category_1 != NULL AND $new_category_1 != "select") {
        // The next 4 if statements form a category string that will eventually go away.
        // Once I can setup a working category system.
        if ($new_category_2 != "" AND $new_category_2 != "Select:" AND $cat_stop == 0) {
            $new_cat = $new_cat . ":" . $new_category_2;
        }
        else {
            $cat_stop = 1;
        }
        if ($new_category_3 != "" AND $new_category_3 != "Select:" AND $cat_stop == 0) {
            $new_cat = $new_cat . ":" . $new_category_3;
        }
        else {
            $cat_stop = 1;
        }
        if ($new_category_4 != "" AND $new_category_4 != "Select:" AND $cat_stop == 0) {
            $new_cat = $new_cat . ":" . $new_category_4;
        }
        else {
            $cat_stop = 1;
        }
        if ($new_category_5 != "" AND $new_category_5 != "Select:" AND $cat_stop == 0) {
            $new_cat = $new_cat . ":" . $new_category_5;
        }
        
        // Grab and format creation date/time
        $datetime = getdate();
        $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
        $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];
       
        // Connect to DB
        $conn = new dbManage();
        
        // Add new entry
        $stmt = 'INSERT INTO `log` (datec, timec, title, entry, usercreated, cat)  VALUES (:datec, :timec, :title, :entry, :usercreated, :cat)';
        $params = array(
            'datec' => $new_date,
            'timec' => $new_time,
            'title' => $new_title,
            'entry' => $new_entry,
            'usercreated' => $_SESSION['userInfo']['userid'], // Don't ask
            'cat' => $new_cat,
        );
        $conn->queryDB($stmt, $params);
        
        echo "Log entry created successfully.";
    }
    else {
        echo '<span class="bad">Log entries must have a title, category, and entry text.</span>';
    }
}