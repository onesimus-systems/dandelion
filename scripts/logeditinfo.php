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
	if ($_SESSION['userInfo'][5] == "guest") {
		header( 'Location: viewlog.php' );
	}
	
	if ($_SESSION['userInfo'][5] === "admin") {
		$admin_link = '| <a href="admin.php">Administration</a>';
	}
	else {
		$admin_link = '';
	}
	
	if ($_SESSION['userInfo'][5] !== "guest") {
		$settings_link = '| <a href="settings.php">Settings</a>';
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
$db = new DB();
$conn = $db->dbConnect();

// Grab log
$stmt = $conn->prepare('SELECT * FROM `log` WHERE `logid` = :logid');
$stmt->execute(array(
    'logid' => $loguid
));
$edit_log_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Return JSON encoded array with log data
echo json_encode($edit_log_info);