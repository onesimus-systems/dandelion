<!DOCTYPE html>

<?php
include 'dbconnect.php';
error_reporting(E_ALL);
ini_set('display_errors', True);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $a_action = $_POST["sub_action"];
		
		if ($a_action == "Clear Session Tokens") {
			$qu = "TRUNCATE TABLE session_token";
			
			if (!mysqli_query($con, $qu)) {
				die('<br /><br />Error: ' . mysqli_error($con));
			}
		}
		elseif ($a_action == "Optimize Database") {
			$qu = "OPTIMIZE TABLE log";
			
			if (!mysqli_query($con, $qu)) {
				die('<br /><br />Error: ' . mysqli_error($con));
			}
		}
        
        //Try SQL statement UPDATE users SET firsttime = 3;
        //Don't use a WHERE statement
		elseif ($a_action == "Set New Features") {
			$grab_users = mysqli_query($con, "SELECT * FROM users");

			while ($row = mysqli_fetch_array($grab_users)) {
				mysqli_query($con, 'UPDATE users SET firsttime = 3 WHERE userid = "'.$row['userid'].'"');
			}
		}
		
		header( 'Location: ../admin.php' );
	}
	else {
		header( 'Location: ../admin.php' );
	}