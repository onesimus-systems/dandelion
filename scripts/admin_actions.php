<?php
/**
 * This script contains any non-user related admin functions.
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

include 'grabber.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $a_action = $_POST["sub_action"];

        $conn = new dbManage();
        
		if ($a_action == "Set New Features") {
            // When the new features page needs to be shown
            // Or a splash screen needs to be shown, set the
            // firsttime column of all non-guest accounts to 3
            $stmt = 'UPDATE `users` SET `firsttime` = 3 WHERE `role` != "guest"';
            $conn->queryDB($stmt, NULL);
		}
		
		elseif ($a_action == "slogan") {
			// Set new slogan
			$stmt = 'UPDATE `settings` SET `value` = :slogan WHERE `name` = "slogan"';
			$params = array(
				'slogan' => $_POST['slogan']		
			);
			$conn->queryDB($stmt, $params);
			
			$_SESSION['settings']['slogan'] = $_POST['slogan'];
		}
		
		header( 'Location: ../admin.phtml' );
	}
	else {
		header( 'Location: ../admin.phtml' );
	}