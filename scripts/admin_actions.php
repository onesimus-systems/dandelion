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

		if ($a_action == "slogan") {
			// Set new slogan
			$stmt = 'UPDATE `'.DB_PREFIX.'settings` SET `value` = :slogan WHERE `name` = "slogan"';
			$params = array(
				'slogan' => $_POST['slogan']		
			);
			$conn->queryDB($stmt, $params);
			
			$_SESSION['app_settings']['slogan'] = $_POST['slogan'];
			
			echo 'Slogan set successfully';
		}
		
		elseif ($a_action == 'backupdb') {
			include 'backupdb.php';
			backupDB();
		}
		
		elseif ($a_action == 'defaultTheme') {
			// Set new default theme
			$stmt = 'UPDATE `'.DB_PREFIX.'settings` SET `value` = :theme WHERE `name` = "default_theme"';
			$params = array(
				'theme' => $_POST['theme']		
			);
			$conn->queryDB($stmt, $params);
			
			$_SESSION['app_settings']['default_theme'] = $_POST['theme'];
			
			echo 'Default theme set successfully';
		}
}