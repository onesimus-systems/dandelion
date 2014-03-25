<?php
require_once 'grabber.php';

if (!authenticated()) {
	header( 'Location: index.php' );
}

$editedlog = isset($_POST['editlog']) ? $_POST['editlog'] : '';
$editedtitle = isset($_POST['edittitle']) ? $_POST['edittitle'] : '';
$logid  = isset($_POST['choosen']) ? $_POST['choosen'] : '';

if (!empty($editedlog) && !empty($editedtitle) && !empty($logid)) {
	$conn = new dbManage();

	$stmt = 'UPDATE `log` SET `title` = :eTitle, `entry` = :eEntry, `edited` = 1 WHERE `logid` = :logid';
	$params = array(
	    'eTitle' => $editedtitle,
	    'eEntry' => $editedlog,
	    'logid' => $logid
	);
	$conn->queryDB($stmt, $params);
}