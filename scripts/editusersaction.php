<?php
/**
 * This script manages all admin user related actions.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

// Connect to DB
$conn = new dbManage();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	require_once ROOT.'/classes/usersTemp.php';
}