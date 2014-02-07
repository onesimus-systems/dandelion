<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$u_action = isset($_POST['user_action']) ? $_POST['user_action'] : '';
		$u_action2 = isset($_POST['user_action2']) ? $_POST['user_action2'] : '';
		$choosen = isset($_POST['the_choosen_one']) ? vali($_POST['the_choosen_one']) : '';
		$sub_typee = isset($_POST['sub_type']) ? $_POST['sub_type'] : '';
		
		if ($u_action == "none" AND $u_action2 != "none") {
			$u_action = $u_action2;
		}
		elseif ($u_action == $u_action2) {
			$u_action = $u_action2;
		}
		elseif ($u_action2 != "none" AND $u_action != "none") {
			$u_action = "";
			echo "ERROR: Both action boxes had a selection.<br /><br />";
		}
		
		$edituserinfo = mysqli_query($con, 'SELECT * FROM users WHERE userid = "' . $choosen . '"');
		
		$edit_user_info = mysqli_fetch_array($edituserinfo);
		
		if ($u_action == "delete") {
		
			//Delete selected user from DB
			if ($choosen != NULL AND $choosen != "") { ?>
				Are you sure you want to delete user <?php echo $choosen ?>?
				<form method="post">
					<input type="hidden" name="the_choosen_one" value="<?php echo $choosen; ?>" />
					<input type="submit" name="sub_type" value="Yes" />
					<input type="submit" value="No" /><br />
				</form>
			<?php
			}
			else {
				echo "ERROR: No users were selected to delete.<br />";
			}
		}
        
        elseif ($u_action == "cxeesto") {
			//Form to edit Cxeesto status
            $grab_user = mysqli_query($con, 'SELECT * FROM presence WHERE uid = "'.$choosen.'"');
		
			$row = mysqli_fetch_array($grab_user);
            
			if ($choosen != NULL AND $choosen != "") { ?>
			<div id="editform"><br />
			<h2>Edit User Status:</h2>
			<form name="edit_form" method="post">
				<table>
					<tr><td>Name:</td><td><input type="text" name="status_name" value="<?php echo $row['realname']; ?>" autocomplete="off" /></td></tr>
					<tr><td>Status:</td><td><select name="status_s"><option>Set Status:</option><option>Available</option><option>Away From Desk</option><option>At Lunch</option><option>Out for Day</option><option>Out</option><option>Appointment</option><option>Do Not Disturb</option><option>Meeting</option><option>Out Sick</option><option>Vacation</option></select></td></tr>
					<tr><td>Message:</td><td><textarea cols="30" rows="5" name="status_message"><?php echo $row['message']; ?></textarea></td></tr>
					<tr><td>Return:</td><td><input type="text" name="status_return" id="datepick" value="<?php echo $row['return']; ?>" /> Format: MM/DD/YYYY 13:00</td></tr>
				</table>
				<input type="submit" name="sub_type" value="Set Status" />
				<input type="submit" name="sub_type" value="Cancel" />
			</form></div><br />
			<?php
			}
			else {
				echo "ERROR: No users were selected to edit.<br />";
			}
		}
        
		elseif ($u_action == "edit") {
		
			//Form to edit user information
			if ($choosen != NULL AND $choosen != "") { ?>
			<div id="editform"><br />
			<h2>Edit User Information:</h2>
			<form name="edit_form" method="post">
				<table>
					<tr><td>User ID:</td><td><input type="text" name="edit_uid" value="<?php echo $edit_user_info['userid']; ?>" readonly /></td></tr>
					<tr><td>Real Name:</td><td><input type="text" name="edit_real" value="<?php echo $edit_user_info['realname']; ?>" autocomplete="off" /></td></tr>
					<tr><td>Settings ID:</td><td><input type="text" name="edit_sid" value="<?php echo $edit_user_info['settings_id']; ?>" autocomplete="off" /></td></tr>
					<tr><td>Role:</td><td><select name="edit_role"><option value="user" <?=$edit_user_info['role'] == 'user' ? ' selected="selected"' : '';?>>User</option><option value="guest" <?=$edit_user_info['role'] == 'guest' ? ' selected="selected"' : '';?>>Guest</option><option value="admin" <?=$edit_user_info['role'] == 'admin' ? ' selected="selected"' : '';?>>Admin</option></select></td></tr>
					<tr><td>Date Created:</td><td><input type="text" name="edit_date" value="<?php echo $edit_user_info['datecreated']; ?>" /></td></tr>
					<tr><td>First Login:</td><td><input type="text" name="edit_first" value="<?php echo $edit_user_info['firsttime']; ?>" autocomplete="off" /></td></tr>
				</table>
				<input type="submit" name="sub_type" value="Edit" />
				<input type="submit" name="sub_type" value="Cancel" />
			</form></div><br />
			<?php
			}
			else {
				echo "ERROR: No users were selected to edit.<br />";
			}
		}
        
		elseif ($u_action == "add") {
		
			//Form to add a new user
			?>
			<div id="editform"><br />
			<h2>Add a User:</h2>
			<form name="edit_form" method="post">
				<table>
					<tr><td>Username:</td><td><input type="text" name="add_user" autocomplete="off" /></td></tr>
					<tr><td>Password:</td><td><input type="password" name="add_pass" /></td></tr>
					<tr><td>Real Name:</td><td><input type="text" name="add_real" autocomplete="off" /></td></tr>
					<tr><td>Settings ID:</td><td><input type="text" name="add_sid" value="0" autocomplete="off" readonly /></td></tr>
					<tr><td>Role:</td><td><select name="add_role"><option value="user">User</option><option value="guest">Guest</option><option value="admin">Admin</option></select></td></tr>
				</table>
				<input type="submit" name="sub_type" value="Add" />
				<input type="submit" name="sub_type" value="Cancel" />
			</form></div><br />
			<?php
		}
        
		elseif ($u_action == "reset") {
		
			//Form to reset user's password
			if ($choosen != NULL AND $choosen != "") { ?>
			<div id="editform"><br />
			<h2>Reset Password:</h2>
			<form name="edit_form" method="post">
				<table>
					<tr><td>User ID:</td><td><input type="text" name="reset_uid" value="<?php echo $edit_user_info['userid']; ?>" readonly /></td></tr>
					<tr><td>New Password:</td><td><input type="password" name="reset_1" /></td></tr>
					<tr><td>Repeat Password:</td><td><input type="password" name="reset_2" /></td></tr>
				</table>
				<input type="submit" name="sub_type" value="Reset" />
				<input type="submit" name="sub_type" value="Cancel" />
			</form></div><br />
			<?php
			}
			else {
				echo "ERROR: No users were selected to reset password.<br />";
			}
		}
		
        
		if ($sub_typee == "Edit") {
		
			//Edit selected users information
			mysqli_query($con, 'UPDATE users SET realname = "'.vali($_POST['edit_real']).'", settings_id = "'.vali($_POST['edit_sid']).'", role = "'.vali($_POST['edit_role']).'", datecreated = "'.vali($_POST['edit_date']).'", firsttime = "'.vali($_POST['edit_first']).'" WHERE userid = "'.vali($_POST['edit_uid']).'"');
			echo 'User Updated<br />';
		}
        
		elseif ($sub_typee == "Add") {
			$grab_users = mysqli_query($con, 'SELECT * FROM users WHERE username = "'.vali($_POST['add_user']).'"');
		
			$row = mysqli_fetch_array($grab_users);
		
			if ($row == NULL) {
				//Add new user to DB
                $date = new DateTime();
                $date = $date->format('Y-m-d H:i:s');
                
				$add_user = vali($_POST['add_user']);
				$add_pass = sha1(vali($_POST['add_pass']));
				$add_real = vali($_POST['add_real']);
				$add_sid = vali($_POST['add_sid']);
				$add_role = vali($_POST['add_role']);
				$qu = 'INSERT INTO users (userid, username, password, realname, settings_id, role, datecreated) VALUES ("", "'.$add_user.'", "'.$add_pass.'", "'.$add_real.'", "'.$add_sid.'", "'.$add_role.'", "2013-10-11")';
                
				if (!mysqli_query($con, $qu)) {
					die('<br /><br />Error creating user (account): ' . mysqli_error($con));
				}
                
                
                $grab_users = mysqli_query($con, 'SELECT * FROM users WHERE username = "'.vali($_POST['add_user']).'"');
		
                $row = mysqli_fetch_array($grab_users);
                
				$qu2 = 'INSERT INTO presence (uid, realname, status, dmodified) VALUES ("'.$row['userid'].'", "'.$add_real.'", "1", "'.$date.'")';
                
				if (!mysqli_query($con, $qu2)) {
					die('<br /><br />Error creating user (&#264;eesto): ' . mysqli_error($con));
				}
                
				echo 'User Added<br />';
			}
			else {
				echo 'Username already exists!';
			}
		}
        
		elseif ($sub_typee == "Reset") {
			$reset_3 = vali($_POST['reset_1']);
			$reset_4 = vali($_POST['reset_2']);
			
			if ($reset_3 == $reset_4) {
				$reset_3 = sha1($reset_3);
				mysqli_query($con, 'UPDATE users SET password = "'.$reset_3.'" WHERE userid = "'.$_POST['reset_uid'].'"');
				echo 'Password Reset<br />';
			}
			else {
				echo 'New passwords do not match<br />';
			}
		}
        
		elseif ($sub_typee == "Yes") {
		
			mysqli_query($con, 'DELETE FROM users WHERE userid = "'.$choosen.'"');
            mysqli_query($con, 'DELETE FROM presence WHERE uid = "'.$choosen.'"');
			echo "Action Taken: User Deleted<br /><br />";
		}
        
        elseif ($sub_typee == "Set Status") {
            $date = new DateTime();
            $date = $date->format('Y-m-d H:i:s');
            
            $stat_user = vali($_POST['status_name']);
            $stat_s = vali($_POST['status_s']);
            $stat_message = vali($_POST['status_message']);
            $stat_return = vali($_POST['status_return']);
            
            switch($stat_s) {
                case "Available":
                    $stat_s = 1;
                    break;
                case "Away From Desk":
                    $stat_s = 2;
                    break;
                case "At Lunch":
                    $stat_s = 3;
                    break;
                case "Out for Day":
                    $stat_s = 4;
                    break;
                case "Out":
                    $stat_s = 5;
                    break;
                case "Appointment":
                    $stat_s = 6;
                    break;
                case "Do Not Disturb":
                    $stat_s = 7;
                    break;
                case "Meeting":
                    $stat_s = 8;
                    break;
                case "Out Sick":
                    $stat_s = 9;
                    break;
                case "Vacation":
                    $stat_s = 10;
                    break;
                default:
                    $stat_s = 1;
                    break;
            }
            
            if ($stat_s == 1) {
                $stat_return = "00:00:00";
            }
            
            if (!mysqli_query($con, 'UPDATE `presence` SET `message` = "'.$stat_message. '", `status` = "'.$stat_s.'", `return` = "'.$stat_return.'", `dmodified` = "'.$date.'" WHERE `realname` = "'.$stat_user.'"')){
                die('Error setting status: ' . mysqli_error($con));
            }
            
			echo "Action Taken: User Status Updated<br /><br />";
		}
	}