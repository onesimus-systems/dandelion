<?php
/**
 * This file goes to the database and grabs the list of recent logs
 * and then sends them to readlog.php to display them.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 27, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

include_once 'grabber.php'; // Required for accessing the DB
include_once 'readlog.php';

// Authenticate user, if fail go to login page
if (!authenticated()) {
    header( 'Location: index.php' );
}

$conn = new dbManage;

// Initialize Variables
$pageOffset = isset($_POST['pageOffset']) ? $_POST['pageOffset'] : '0'; // Supplied via AJAX

// For later use to specify a page number instead of offset ;)
if (isset($_POST['page'])) {
    $pageOffset = ($_POST['page'] * $_SESSION['userInfo']['showlimit']) - $_SESSION['userInfo']['showlimit'];
}

$pageOffset = $pageOffset<0 ? '0' : $pageOffset; // If somehow the offset is < 0, make it 0

// Grab row count of log table to determine offset
$stmt = 'SELECT COUNT(*) FROM `log`';
$logSize = $conn->queryDB($stmt, NULL);

// If the next page offset is > than the row count (which shouldn't happen
// any more thanks to some logic in readlog.php), make the offset the last
// offset, so the current offset - the user page show limit.
if ($pageOffset > $logSize[0]['COUNT(*)']) {
    $pageOffset = $pageOffset - $_SESSION['userInfo']['showlimit'];
}

// When using a SQL LIMIT, the parameter MUST be an integer.
// To accomplish this the PDO constant PARAM_INT was passed
$stmt = 'SELECT * FROM `log` ORDER BY `logid` DESC LIMIT :pO,:lim';
$params = array(
    'lim' => ((int) trim($_SESSION['userInfo']['showlimit'])),
    'pO' => ((int) trim($pageOffset))
);
    
$grab_logs = $conn->queryDB($stmt, $params, PDO::PARAM_INT);

$isFiltered = false; // Show paging controls

$dis = new DisplayLogs;
$dis->display($grab_logs);