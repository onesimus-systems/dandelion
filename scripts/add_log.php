<?php
/**
 * This script is called via AJAX to create a new log entry.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

include 'grabber.php';

if (!authenticated()) {
	header( 'Location: index.php' );
}

$blacklist = array('ajmartin');

if (!in_array($_SESSION['userInfo']['username'], $blacklist)) { // This is the skeleton of an eventual blacklist/rights management
    // Grab all the variables from the POST array
    $new_title = isset($_POST['add_title']) ? $_POST['add_title'] : '';
    $new_entry = isset($_POST['add_entry']) ? $_POST['add_entry'] : '';
    $new_category = isset($_POST['cat']) ? $_POST['cat'] : '';

    // Check that all required fields have been entered
    if ($new_title != NULL AND $new_title != "" AND $new_entry != NULL AND $new_entry != "" AND $new_category != NULL AND $new_category != "Select:") {
        $datetime = getdate();
        $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
        $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];

        $conn = new dbManage();
        
        // Add new entry
        $stmt = 'INSERT INTO `'.DB_PREFIX.'log` (datec, timec, title, entry, usercreated, cat)  VALUES (:datec, :timec, :title, :entry, :usercreated, :cat)';
        $params = array(
            'datec' => $new_date,
            'timec' => $new_time,
            'title' => $new_title,
            'entry' => $new_entry,
            'usercreated' => $_SESSION['userInfo']['userid'],
            'cat' => $new_category,
        );
        $conn->queryDB($stmt, $params);
        
        echo "Log entry created successfully.";
    }
    else {
        echo '<span class="bad">Log entries must have a title, category, and entry text.</span>';
    }
}