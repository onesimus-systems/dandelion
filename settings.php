<!DOCTYPE html>

<?php include 'scripts/permset.php'; ?>

<html>
	<head>
		<meta charset="utf-8" />
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
		<title>Dandelion Web Log</title>
	</head>
	<body>
        <header>
            <?php include 'scripts/header.php'; ?>
        </header>
		
		<h3>User Settings</h3>
		
		<?php
        $limit = $user_info['showlimit'];
        
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$u_action = isset($_POST['set_action']) ? $_POST['set_action'] : '';
				$sub_action = isset($_POST['sub_act']) ? $_POST['sub_act'] : '';
				
				if ($u_action == "Reset Password") {
				
					//Form to reset user's password
					?>
					<div id="editform"><br />
					<h2>Reset Password for <?php echo $user_info['realname']; ?>:</h2>
					<form name="edit_form" method="post">
						<table>
							<tr><td>User ID:</td><td><input type="hidden" name="reset_uid" value="<?php echo $user_info['userid']; ?>" readonly /></td></tr>
							<tr><td>New Password:</td><td><input type="password" name="reset_1" /></td></tr>
							<tr><td>Repeat Password:</td><td><input type="password" name="reset_2" /></td></tr>
						</table>
						<input type="submit" name="sub_act" value="Reset" />
					</form></div><br />
					<?php
				}
                else if ($u_action == "Save Show Limit") {
                    $showlimit = vali($_POST['show_limit']);
                    
                    if ($showlimit >= 5 AND $showlimit <= 250) {
                        mysqli_query($con, 'UPDATE users SET showlimit = "'.$showlimit.'" WHERE userid = "'.$user_info['userid'].'"');
                        
                        echo "Limit Saved<br /><br />";
                        $limit = $showlimit;
                    }
                    else {
                        echo "Please choose a number between 5-250 for log view limit.<br /><br />";
                    }
                }
				
				if ($sub_action == "Reset") {
					$reset_3 = vali($_POST['reset_1']);
					$reset_4 = vali($_POST['reset_2']);
					
					if ($reset_3 == $reset_4) {
						$reset_3 = sha1($reset_3);
						mysqli_query($con, 'UPDATE users SET password = "'.$reset_3.'" WHERE userid = "'.$_POST['reset_uid'].'"');
						echo 'Password Reset<br /><br />';
					}
					else {
						echo 'New passwords do not match<br /><br />';
					}
				}
			}
		?>
        
        Your username is <?php echo $user_info['username']; ?>.<br /><br />
        
        <?php
            if ($user_info['role'] != "guest") {
            ?>
            <form method="post">
                <input type="submit" name="set_action" value="Reset Password" /><br /><br />
                <input type="text" name="show_limit" size="3" value="<?php echo $limit; ?>" />
                <input type="submit" name="set_action" value="Save Show Limit" />
            </form>
            <?php
            }
        ?>
        
        <br />More settings to come as development continues.
        
        <footer>
            <p id="credits">&copy; 2013 Daedalus Computer Services</p>
        </footer>
	</body>
</html>
