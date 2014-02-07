<!DOCTYPE html>

<?php
include 'scripts/permset.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$sub_action = isset($_POST['sub_act']) ? $_POST['sub_act'] : '';
	
	if ($sub_action == "Reset") {
		$reset_3 = isset($_POST['reset_1']) ? $_POST['reset_1'] : '';
		$reset_4 = isset($_POST['reset_2']) ? $_POST['reset_2'] : '';
		
		if ($reset_3 == $reset_4) {
			$reset_3 = sha1($reset_3);
			mysqli_query($con, 'UPDATE users SET password = "'.$reset_3.'" WHERE userid = "'.$_POST['reset_uid'].'"');
			mysqli_query($con, 'UPDATE users SET firsttime = 1 WHERE userid = "'.$user_info['userid'].'"');
			echo 'Password Reset<br /><br />';
			header( 'Location: scripts/logout.php' );
		}
		else {
			echo 'New passwords do not match<br /><br />';
		}
	}
}
?>
<html>
	<head>
		<meta charset="utf-8" />
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
        <link rel="stylesheet" type="text/css" href="styles/tutorial.css" />
		<title>Dandelion Web Log - Tutorial</title>
	</head>
	<body>
        <header>
            <h1 class="t_cen">Dandelion Web Log - v 2</h1>
<p class="t_cen">Welcome, <?php echo $realname; ?> <a href="scripts/logout.php">Logout</a></p>
        </header>
        
        <p class="le">This is your first time logging into Dandelion. Please reset your password:</p>
		
		<div id="editform"><br />
			<form name="edit_form" method="post">
				<input type="hidden" name="reset_uid" value="<?php echo $user_info['userid']; ?>" readonly />
				<table>
					<tr><td>New Password:</td><td><input type="password" name="reset_1" /></td></tr>
					<tr><td>Repeat Password:</td><td><input type="password" name="reset_2" /></td></tr>
				</table>
				<input type="submit" name="sub_act" value="Reset" />
			</form>
		</div>
		
		<br />
        
        <footer>
            <p id="credits" class="t_cen">&copy; 2013 Daedalus Computer Services</p>
        </footer>
	</body>
</html>
