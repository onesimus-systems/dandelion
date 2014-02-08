<?php
/*
 * Lee Keitel
 * January 28, 2014
 *
 * This script manages all admin user related actions.
 * 
*/

// Connect to DB
$db = new DB();
$conn = $db->dbConnect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$u_action  = isset($_POST['user_action']) ? $_POST['user_action'] : '';
		$u_action2 = isset($_POST['user_action2']) ? $_POST['user_action2'] : '';
		$choosen   = isset($_POST['the_choosen_one']) ? $_POST['the_choosen_one'] : '';
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
        
        $stmt = $conn->prepare('SELECT * FROM `users` WHERE `userid` = :userid');
        $stmt->execute(array(
            'userid' => $choosen
        ));
        $edit_user_info = $stmt->fetch(PDO::FETCH_ASSOC);
		
        //-----FIRST LEVEL ACTIONS-------//
        
		if ($u_action == "delete") { // Confirm user delete
		
			// Delete selected user from DB
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
        
        elseif ($u_action == "cxeesto") { // Show status update form
			// Form to edit Cxeesto status.
            $stmt = $conn->prepare('SELECT * FROM `presence` WHERE `uid` = :userid');
            $stmt->execute(array(
                'userid' => $choosen
            ));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
			if ($choosen != NULL AND $choosen != "") { ?>
			<div id="editform"><br />
			<h2>Edit User Status:</h2>
			<form name="edit_form" method="post">
				<table>
					<tr><td>Name:</td><td><input type="text" name="status_name" value="<?php echo $row['uid']; ?>" autocomplete="off" /></td></tr>
					<tr><td>Status:</td><td>
                        <select name="status_s">
                            <option>Set Status:</option>
                            <option>Available</option>
                            <option>Away From Desk</option>
                            <option>At Lunch</option>
                            <option>Out for Day</option>
                            <option>Out</option>
                            <option>Appointment</option>
                            <option>Do Not Disturb</option>
                            <option>Meeting</option>
                            <option>Out Sick</option>
                            <option>Vacation</option>
                        </select></td></tr>
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
        
		elseif ($u_action == "edit") { // Show edit user form
		
			// Form to edit user information
			if ($choosen != NULL AND $choosen != "") { ?>
			<div id="editform"><br />
			<h2>Edit User Information:</h2>
			<form name="edit_form" method="post">
				<table>
					<tr><td>User ID:</td><td><input type="text" name="edit_uid" value="<?php echo $edit_user_info['userid']; ?>" readonly /></td></tr>
					<tr><td>Real Name:</td><td><input type="text" name="edit_real" value="<?php echo $edit_user_info['realname']; ?>" autocomplete="off" /></td></tr>
					<tr><td>Settings ID:</td><td><input type="text" name="edit_sid" value="<?php echo $edit_user_info['settings_id']; ?>" autocomplete="off" /></td></tr>
					<tr><td>Role:</td><td>
                        <select name="edit_role">
                            <option value="user" <?=$edit_user_info['role'] == 'user' ? ' selected="selected"' : '';?>>User</option>
                            <option value="guest" <?=$edit_user_info['role'] == 'guest' ? ' selected="selected"' : '';?>>Guest</option>
                            <option value="admin" <?=$edit_user_info['role'] == 'admin' ? ' selected="selected"' : '';?>>Admin</option>
                        </select></td></tr>
					<tr><td>Date Created:</td><td><input type="text" name="edit_date" value="<?php echo $edit_user_info['datecreated']; ?>" readonly /></td></tr>
					<tr><td>First Login:</td><td><input type="text" name="edit_first" value="<?php echo $edit_user_info['firsttime']; ?>" autocomplete="off" /></td></tr>
				</table>
				<input type="submit" name="sub_type" value="Save Edit" />
				<input type="submit" name="sub_type" value="Cancel" />
			</form></div><br />
			<?php
			}
			else {
				echo "ERROR: No users were selected to edit.<br />";
			}
		}
        
		elseif ($u_action == "add") { // Show create user form
		
			// Form to add a new user
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
        
		elseif ($u_action == "reset") { // Show password reset form
		
			// Form to reset user's password
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
		
        //-----END FIRST LEVEL ACTIONS-------//
        //-----SECOND LEVEL ACTIONS-------//
        
		if ($sub_typee == "Save Edit") { // Edit user data
			// Edit selected users information
            try {
                $stmt = $conn->prepare('UPDATE `users` SET `realname` = :realname, `settings_id` = :s_id, `role` = :role, `firsttime` = :first WHERE `userid` = :userid');
                $stmt->execute(array(
                    'realname' => $_POST['edit_real'],
                    's_id' => $_POST['edit_sid'],
                    'role' => $_POST['edit_role'],
                    'first' => $_POST['edit_first'],
                    'userid' => $_POST['edit_uid']
                ));
                
                $stmt = $conn->prepare('UPDATE `presence` SET `realname` = :realname WHERE `uid` = :userid');
                $stmt->execute(array(
                    'realname' => $_POST['edit_real'],
                    'userid' => $_POST['edit_uid']
                ));
            
                echo 'User Updated<br />';
            } catch(PDOException $e) {
                echo 'Error editing user.';
            }
		}
        
		elseif ($sub_typee == "Add") { // Create new user
            // Check if username already exists            
            try {
                $stmt = $conn->prepare('SELECT * FROM `users` WHERE `username` = :username');
                $stmt->execute(array(
                    'username' => $_POST['add_user']
                ));
                $row = $stmt->fetch();
                
                if ($row == NULL) {
                    //Add new user to DB
                    $date = new DateTime();
                    //$date = $date->format('Y-m-d H:i:s');
                    
                    $add_user = $_POST['add_user'];
                    $add_pass = password_hash($_POST['add_pass'], PASSWORD_BCRYPT);
                    $add_real = $_POST['add_real'];
                    $add_sid = $_POST['add_sid'];
                    $add_role = $_POST['add_role'];
                    $qu = 'INSERT INTO users (username, password, realname, settings_id, role, datecreated) VALUES (:username, :password, :realname, :s_id, :role, :datecreated)';
                    
                    $stmt = $conn->prepare($qu);
                    $stmt->execute(array(
                        'username' => $add_user,
                        'password' => $add_pass,
                        'realname' => $add_real,
                        's_id' => $add_sid,
                        'role' => $add_role,
                        'datecreated' => $date->format('Y-m-d')
                    ));
                    
                    $stmt = $conn->prepare('SELECT * FROM users WHERE username = :user');
                    $stmt->execute(array(
                        'user' => $add_user
                    ));
            
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $qu2 = 'INSERT INTO presence (uid, realname, status, dmodified) VALUES (:uid, :real, 1, :date)';
                    
                    $stmt = $conn->prepare($qu2);
                    $stmt->execute(array(
                        'uid' => $row['userid'],
                        'real' => $add_real,
                        'date' => $date->format('Y-m-d H:i:s')
                    ));
                    
                    echo 'User Added<br />';
                }

                else {
                    echo 'Username already exists!';
                }
                
            } catch(PDOException $e) {
                echo 'Database error 42';
            }
		}
        
		elseif ($sub_typee == "Reset") { // Reset user password
			$reset_3 = $_POST['reset_1'];
			$reset_4 = $_POST['reset_2'];
			
			if ($reset_3 == $reset_4) {
				$reset_3 = password_hash($reset_3, PASSWORD_BCRYPT); // Hash the password if they match

                try {
                    // Update record with new password
                    $stmt = $conn->prepare('UPDATE `users` SET `password` = :newpass WHERE `userid` = :myID');
                    $stmt->execute(array(
                        'newpass' => $reset_3,
                        'myID' => $_POST['reset_uid']
                    ));
                    
                    echo 'Password change successful.<br /><br />';
                    
                } catch(PDOExeception $e) {
                    echo 'Error updating password.<br /><br />';
                }
			}
			else {
				echo 'New passwords do not match<br />';
			}
		}
        
		elseif ($sub_typee == "Yes") { // Delete user

            try {
                $stmt = $conn->prepare('DELETE FROM `users` WHERE `userid` = :userid');
                $stmt->execute(array(
                    'userid' => $choosen
                ));
                
                $stmt = $conn->prepare('DELETE FROM `presence` WHERE `uid` = :userid');
                $stmt->execute(array(
                    'userid' => $choosen
                ));
                
                echo "Action Taken: User Deleted<br /><br />";
                
            } catch(PDOException $e) {
                echo 'Error deleting user. ' . $e;
            }
		}
        
        elseif ($sub_typee == "Set Status") { // Change user Cxeesto status
            $date = new DateTime();
            $date = $date->format('Y-m-d H:i:s');
            
            $stat_user = $_POST['status_name'];
            $stat_s = $_POST['status_s'];
            $stat_message = $_POST['status_message'];
            $stat_return = $_POST['status_return'];
            
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
            
            try {
                $stmt = $conn->prepare('UPDATE `presence` SET `message` = :message, `status` = :status, `return` = :return, `dmodified` = :date WHERE `uid` = :userid');
                $stmt->execute(array(
                    'message' => $stat_message,
                    'status' => $stat_s,
                    'return' => $stat_return,
                    'date' => $date,
                    'userid' => $stat_user
                ));
            
                echo 'User Status Updated<br />';
            } catch(PDOException $e) {
                echo 'Error updating user status.';
            }
		}
        
        //-----END SECOND LEVEL ACTIONS-------//
	}