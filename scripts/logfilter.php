<?php
/*
 * Lee Keitel
 * January 27, 2014
 *
 * This page is responsible for filtering the log.
 * Users may filter by keyword or date or both.
 * When this page is called the autorefresh is
 * disabled in the JS and then this does its magic.
*/

include_once 'grabber.php';
include_once 'readlog.php';

// Authenticate user, if fail go to login page
if (!checkLogIn()) {
    header( 'Location: index.php' );
}

$filter = isset($_POST['filter']) ? $_POST['filter'] : '';
$keyw = isset($_POST['keyw']) ? $_POST['keyw'] : '';
$dates = isset($_POST['dates']) ? $_POST['dates'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

// Connect to DB
$conn = new dbManage;

if ($type == "") {
    ?>
    
    <form>
        <h3>**Filter applied: <?php echo $filter; ?>**</h3>
        <input type="button" value="Clear Filter" onClick="refreshLog('clearf')" />
    </form>

    <?php
    $stmt = 'SELECT * FROM `log` WHERE `cat` LIKE :filter ORDER BY `logid` DESC';
    $params = array(
        'filter' => "%".$filter."%"
    );
    $grab_logs = $conn->queryDB($stmt, $params);
}

else {
    // Keyword search
    if ($type == "keyw") {
        $message = $keyw;
        
        $stmt = 'SELECT * FROM `log` WHERE `title` LIKE :keyw or `entry` LIKE :keyw ORDER BY `logid` DESC';
        $params = array(
            'keyw' => "%".$keyw."%"
        );
        $grab_logs = $conn->queryDB($stmt, $params);
    }
    // Logs made on certain date
    else if ($type == "dates") {
        $message = $dates;
        
        $stmt = 'SELECT * FROM `log` WHERE `datec`=:dates ORDER BY `logid` DESC';
        $params = array(
            'dates' => $dates
        );
        $grab_logs = $conn->queryDB($stmt, $params);
    }
    // Logs made on certain day containing keyword
    else {
        $message = $keyw.' on '.$dates;

        $stmt = 'SELECT * FROM `log` WHERE (`title` LIKE :keyw or `entry` LIKE :keyw) and `datec`=:dates ORDER BY `logid` DESC';
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