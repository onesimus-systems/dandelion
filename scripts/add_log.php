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

if ($_SESSION['rights']['createlog']) {
    // Grab all the variables from the POST array
    $new_title = isset($_POST['add_title']) ? $_POST['add_title'] : '';
    $new_entry = isset($_POST['add_entry']) ? $_POST['add_entry'] : '';
    $new_category = isset($_POST['cat']) ? $_POST['cat'] : NULL;

    // Check that all required fields have been entered
    if ($new_title != NULL AND $new_title != "" AND
        $new_entry != NULL AND $new_entry != "" AND
        $new_category != NULL AND $new_category != "Select:" AND $new_category != 'false')
    {
        $datetime = getdate();
        $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
        $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];

        $conn = new dbManage();
        
        // Add new entry
        $stmt = 'INSERT INTO `'.DB_PREFIX.'log` (datec, timec, title, entry, usercreated, cat)  VALUES (:datec, :timec, :title, :entry, :usercreated, :cat)';
        $params = array(
            'datec' => $new_date,
            'timec' => $new_time,
            'title' => urldecode($new_title),
            'entry' => urldecode($new_entry),
            'usercreated' => $_SESSION['userInfo']['userid'],
            'cat' => urldecode($new_category),
        );
        $conn->queryDB($stmt, $params);
        
        echo "Log entry created successfully.";
    }
    else {
        echo '<span class="bad">Log entries must have a title, category, and entry text.</span>';
    }
}

else {
    echo 'This account can\'t create logs.';
}