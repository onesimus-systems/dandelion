<?php
/*
 * Lee Keitel
 * January 27, 2014
 *
 * This file is goes to the database and grabs the list of recent logs
 * and then 'sends' them to readlog.php to display them.
*/

include_once 'dbconnect.php'; // Required for accessing the DB
include_once 'readlog.php';

// Authenticate user, if fail go to login page
if (!checkLogIn()) {
    header( 'Location: index.php' );
}

// Initialize Variables
$pageOffset = isset($_POST['pageOffset']) ? $_POST['pageOffset'] : '0'; // Supplied via AJAX

// For later use to specify a page number instead of offset ;)
if (isset($_POST['page'])) {
    $pageOffset = $_POST['page'] * $_SESSION['userInfo'][8];
}

$pageOffset = $pageOffset<0 ? '0' : $pageOffset; // If somehow the offset is < 0, make it 0

// Connect to DB
$conn = new dbManage;

// Grab row count of log table to determine offset
$stmt = 'SELECT COUNT(*) FROM `log`';
$logSize = $conn->queryDB($stmt, NULL);

// If the next page offset is > than the row count (which shouldn't happen
// any more thanks to some logic in readlog.php), make the offset the last
// offset, so the current offset - the user page show limit.
if ($pageOffset > $logSize[0]['COUNT(*)']) {
    $pageOffset = $pageOffset - $_SESSION['userInfo']['showlimit'];
}

// When using a LIMIT, the parameter MUST be an integer.
// To accomplish this the bindValue method was needed while parsing
// the user setting as an integer.
try {
    $stmt = 'SELECT * FROM `log` ORDER BY `logid` DESC LIMIT :pO,:lim';
    /*
    $grab_logs->bindValue(':lim', (int) trim($_SESSION['userInfo'][8]), PDO::PARAM_INT); // Show amount
    $grab_logs->bindValue(':pO', (int) trim($pageOffset), PDO::PARAM_INT); // Row offset
    $grab_logs->execute();*/
    $params = array(
        ':lim' => ((int) trim($_SESSION['userInfo']['showlimit'])),
        ':pO' => ((int) trim($pageOffset))
    );
    
    $grab_logs = $conn->queryDBbind($stmt, $params);
    
} catch(PDOException $e) {
    echo 'Database error';
}

$isFiltered = false; // Show paging controls

$dis = new DisplayLogs;
$dis->display($grab_logs);