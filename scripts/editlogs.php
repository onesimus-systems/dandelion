<?php
include 'grabber.php';

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

$editedlog = isset($_POST['editlog']) ? $_POST['editlog'] : '';
$editedtitle = isset($_POST['edittitle']) ? $_POST['edittitle'] : '';
$choosen  = isset($_POST['choosen']) ? $_POST['choosen'] : '';

// Connect to DB
$conn = new dbManage();

// Update the database
$stmt = 'UPDATE `log` SET `title` = :eTitle, `entry` = :eEntry, `edited` = 1 WHERE `logid` = :leLogID';
$params = array(
    'eTitle' => $editedtitle,
    'eEntry' => $editedlog,
    'leLogID' => $choosen
);
$conn->queryDB($stmt, $params);