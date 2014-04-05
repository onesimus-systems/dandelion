<?php
/**
  * Handle requests to save edits to log entries
  *
  * This file is a part of Dandelion
  * 
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

require_once 'grabber.php';

if (!authenticated()) {
	header( 'Location: index.php' );
}

$editedlog = isset($_POST['editlog']) ? $_POST['editlog'] : '';
$editedtitle = isset($_POST['edittitle']) ? $_POST['edittitle'] : '';
$logid  = isset($_POST['choosen']) ? $_POST['choosen'] : '';

if (!empty($editedlog) && !empty($editedtitle) && !empty($logid)) {
	$conn = new dbManage();

	$stmt = 'UPDATE `'.DB_PREFIX.'log` SET `title` = :eTitle, `entry` = :eEntry, `edited` = 1 WHERE `logid` = :logid';
	$params = array(
	    'eTitle' => $editedtitle,
	    'eEntry' => $editedlog,
	    'logid' => $logid
	);
	$conn->queryDB($stmt, $params);
}