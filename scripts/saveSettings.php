<?php
/**
  * Handle requests to save user's settings
  *
  * This file is a part of Dandelion
  * 
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

$limit = $_SESSION['userInfo']['showlimit'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$u_action = isset($_POST['set_action']) ? $_POST['set_action'] : '';
	$sub_action = isset($_POST['sub_act']) ? $_POST['sub_act'] : '';
	
	if ($u_action == "Reset Password") {
	
		//Form to reset user's password
		?>
		<div id="editform">
		<h2>Reset Password for <?php echo $_SESSION['userInfo']['realname']; ?>:</h2>
		<form name="edit_form" method="post">
			<table>
				<tr><td>New Password:</td><td><input type="password" name="reset_1" /></td></tr>
				<tr><td>Repeat Password:</td><td><input type="password" name="reset_2" /></td></tr>
			</table>
			<input type="submit" name="sub_act" value="Reset" />
		</form></div><br />
		<?php
	}

	else if ($u_action == "Save Limit") {
	    $showlimit = $_POST['show_limit'];
	    
	    if ($showlimit >= 5 AND $showlimit <= 250) {
			// Connect to DB
			$conn = new dbManage();
			
			// Update record with new record limit
			$stmt = 'UPDATE `users` SET `showlimit` = :newlimit WHERE `userid` = :myID';
			$params = array(
			    'newlimit' => $showlimit,
			    'myID' => $_SESSION['userInfo']['userid']
			);
			$conn->queryDB($stmt, $params);
			
			echo 'Show limit change successful.<br /><br />';
			
			$limit = $_SESSION['userInfo']['showlimit'] = $showlimit;
	    }
	    else {
			echo "Please choose a number between 5-250 for log view limit.<br /><br />";
	    }
	}
	
	else if ($u_action == 'Save Theme') {
		$newTheme = isset($_POST['userTheme']) ? $_POST['userTheme'] : 'default';
		
		$conn = new dbManage();
		
		$stmt = 'UPDATE `users` SET `theme` = :theme WHERE `userid` = :myID';
		$params = array(
		    'theme' => $newTheme,
		    'myID' => $_SESSION['userInfo']['userid']
		);
		$conn->queryDB($stmt, $params);
		
		$_SESSION['userInfo']['theme'] = $newTheme;
		
		header('Location: settings.phtml');
	}
					
	// Check and save new password
	if ($sub_action == "Reset") {
		$reset_3 = $_POST['reset_1'];
		$reset_4 = $_POST['reset_2'];
		
		if ($reset_3 == $reset_4) {
			$reset_3 = password_hash($reset_3, PASSWORD_BCRYPT); // Hash the password if they match
			
			// Connect to DB
			$conn = new dbManage();
			
			// Update record with new password
			$stmt = 'UPDATE `users` SET `password` = :newpass WHERE `userid` = :myID';
			$params = array(
			    'newpass' => $reset_3,
			    'myID' => $_SESSION['userInfo']['userid']
			);
			$conn->queryDB($stmt, $params);
			
			echo 'Password change successful.<br /><br />';
	
		}
		else {
			echo 'New passwords do not match<br /><br />';
		}
	}
}