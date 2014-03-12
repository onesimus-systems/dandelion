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
    $new_category = isset($_POST['cat']) ? $_POST['cat'] : '';

    // Check that all required fields have been entered
    if ($new_title != NULL AND $new_title != "" AND $new_entry != NULL AND $new_entry != "" AND $new_category != NULL AND $new_category != "select") {
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
            'cat' => $new_category,
        );
        $conn->queryDB($stmt, $params);
        
        echo "Log entry created successfully.";
    }
    else {
        echo '<span class="bad">Log entries must have a title, category, and entry text.</span>';
    }
}