<?php
/**
 * This page handles the Cxeesto platform.
 * It is responsible for updating the status board,
 * committing status updates for users, etc.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 27, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

// FIX THIS! ROOT should be defined with grabber, no where else
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(__FILE__)));	
}
require_once ROOT.'/classes/cxeesto.php';

/* $windowed is defined in presenceWindow.php before this is called,
 * so this variable is already set as 1 when the windows opens.
 * This tells us that the user is looking at the windowed version.
**/
$windowed = isset($windowed) ? $windowed : '0';
$windowedt = isset($_POST['windowedt']) ? $_POST['windowedt'] : '0';
$setorno = isset($_POST['setorno']) ? $_POST['setorno'] : '';

$updateCxeesto = new cxeesto;

if ($setorno == '') {
    $updateCxeesto->refreshStatus($windowed, $windowedt);
}
else {
	$returntime = isset($_POST['returntime']) ? $_POST['returntime'] : '00:00:00';
	$message = isset($_POST['message']) ? $_POST['message'] : '';
	
	$updateCxeesto->updateStatus($message, $setorno, $returntime);
    $updateCxeesto->refreshStatus($windowed, $windowedt);
}

