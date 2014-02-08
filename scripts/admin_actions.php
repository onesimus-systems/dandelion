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
        $db = new DB();
        $conn = $db->dbConnect();
		
		if ($a_action == "Clear Session Tokens") {
            // Clear session token table
            // This will not log people out
            // until their PHP session expires.
            // TODO: Create a force logout system
            try {
                $stmt = $conn->prepare('TRUNCATE TABLE session_token');
                $stmt->execute();
            } catch(PDOException $e) {
                echo 'Error clearing session tokens.';
            }
		}
		elseif ($a_action == "Optimize Database") {
            // Optimize log table
            try {
                $stmt = $conn->prepare('OPTIMIZE TABLE log');
                $stmt->execute();
            } catch(PDOException $e) {
                echo 'Error clearing session tokens.';
            }
		}
        
		elseif ($a_action == "Set New Features") {
            // When the new features page needs to be shown
            // Or a splash screen needs to be shown, set the
            // firsttime column of all non-guest accounts to 3
            try {
                $stmt = $conn->prepare('UPDATE `users` SET `firsttime` = 3 WHERE `role` != "guest"');
                $stmt->execute();
            } catch(PDOException $e) {
                echo 'Error setting variable.';
            }
		}
		
		header( 'Location: ../admin.phtml' );
	}
	else {
		header( 'Location: ../admin.phtml' );
	}