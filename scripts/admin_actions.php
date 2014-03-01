<?php
/*
 * Lee Keitel
 * January 28, 2014
 *
 * This script contains any non-user related
 * admin functions.
*/

include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $a_action = $_POST["sub_action"];
        
        // Connect to DB
        $conn = new dbManage();
		
		if ($a_action == "Clear Session Tokens") {
            // Clear session token table
            // This will not log people out
            // until their PHP session expires.
            // TODO: Create a force logout system
            $stmt = 'TRUNCATE TABLE session_token';
            $conn->queryDB($stmt, NULL);
		}
        
		elseif ($a_action == "Set New Features") {
            // When the new features page needs to be shown
            // Or a splash screen needs to be shown, set the
            // firsttime column of all non-guest accounts to 3
            $stmt = 'UPDATE `users` SET `firsttime` = 3 WHERE `role` != "guest"';
            $conn->queryDB($stmt, NULL);
		}
		
		header( 'Location: ../admin.phtml' );
	}
	else {
		header( 'Location: ../admin.phtml' );
	}