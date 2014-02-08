<?php
/*
 * Lee Keitel
 * January 28, 2014
 *
 * This script is called via AJAX to grab log data
 * when a use wants to edit a log. It returns the
 * data as a JSON encoded array.
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

$loguid = isset($_POST['loguid']) ? $_POST['loguid'] : '';

// Connect to DB
$conn = new dbManage();

// Grab log
$stmt = 'SELECT * FROM `log` WHERE `logid` = :logid';
$params = array(
    'logid' => $loguid
);
$edit_log_info = $conn->queryDB($stmt, $params);

// Return JSON encoded array with log data
echo json_encode($edit_log_info);