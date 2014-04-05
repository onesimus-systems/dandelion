<?php
/**
 * This page is responsible for filtering the log.
 * Users may filter by keyword or date or both or category.
 * When this page is called the autorefresh is
 * disabled in the JS and then this does its magic.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 27, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

include_once 'grabber.php';
include_once '../classes/DisplayLogs.php';

// Authenticate user, if fail go to login page
if (authenticated()) {
	$filter = isset($_POST['filter']) ? $_POST['filter'] : '';
	$keyw = isset($_POST['keyw']) ? $_POST['keyw'] : '';
	$dates = isset($_POST['dates']) ? $_POST['dates'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	
	$conn = new dbManage;
	
	// Category Search
	if ($type == "") {
	    ?>
		    <form>
		        <h3>**Filter applied: <?php echo $filter; ?>**</h3>
		        <input type="button" value="Clear Filter" onClick="refreshLog('clearf')" />
		    </form>
	    <?php
	    $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `cat` LIKE :filter ORDER BY `logid` DESC';
	    $params = array(
	        'filter' => "%".$filter."%"
	    );
	    $grab_logs = $conn->queryDB($stmt, $params);
	}
	
	else {
	    // Keyword search
	    if ($type == "keyw") {
	        $message = $keyw;
	        
	        $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `title` LIKE :keyw or `entry` LIKE :keyw ORDER BY `logid` DESC';
	        $params = array(
	            'keyw' => "%".$keyw."%"
	        );
	        $grab_logs = $conn->queryDB($stmt, $params);
	    }
	    // Logs made on certain date
	    else if ($type == "dates") {
	        $message = $dates;
	        
	        $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `datec`=:dates ORDER BY `logid` DESC';
	        $params = array(
	            'dates' => $dates
	        );
	        $grab_logs = $conn->queryDB($stmt, $params);
	    }
	    // Logs made on certain day containing keyword
	    else {
	        $message = $keyw.' on '.$dates;
	
	        $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE (`title` LIKE :keyw or `entry` LIKE :keyw) and `datec`=:dates ORDER BY `logid` DESC';
	        $params = array(
	            'keyw' => "%".$keyw."%",
	            'dates' => $dates
	        );
	        $grab_logs = $conn->queryDB($stmt, $params);
	    }
	    ?>
		    <form>
		        <h3 style="display:inline;">Search results for: <?php echo $message; ?></h3>
		        <input type="button" value="Clear Search" onClick="refreshLog('clearf')" />
		    </form>
	    <?php
	}
	
	$isFiltered = true; // Don't show paging controls
	
	// Display filtered logs
	$dis = new DisplayLogs;
	$dis->display($grab_logs);
}