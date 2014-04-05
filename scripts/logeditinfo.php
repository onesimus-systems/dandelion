<?php
/**
 * This script is called via AJAX to grab log data
 * when a use wants to edit a log. It returns the
 * data as a JSON encoded array.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

include 'grabber.php';

if (authenticated()) {
	$loguid = isset($_POST['loguid']) ? $_POST['loguid'] : '';

	$conn = new dbManage();

	$stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `logid` = :logid';
	$params = array(
	    'logid' => $loguid
	);
	$edit_log_info = $conn->queryDB($stmt, $params);

	echo json_encode($edit_log_info);
}