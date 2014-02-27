<?php
/*
 * Lee Keitel
 * January 28, 2014
 *
 * This script manages all admin user related actions.
 * 
*/

// Connect to DB
$conn = new dbManage();

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
        
        $stmt = 'SELECT * FROM `users` WHERE `userid` = :userid';
        $params = array(
            'userid' => $choosen
        );
        $edit_user_info = $conn->queryDB($stmt, $params);
        $edit_user_info = isset($edit_user_info[0]) ? $edit_user_info[0] : '';
		
        //-----FIRST LEVEL ACTIONS-------//
        
		if ($u_action == "delete") { // Confirm user delete
		
			// Delete selected user from DB
			if ($choosen != NULL AND $choosen != "") { ?>
                <br /><hr width="500">
				Are you sure you want to delete "<?php echo $edit_user_info['realname'] ?>"?<br /><br />
				<form method="post">
					<input type="hidden" name="the_choosen_one" value="<?php echo $choosen; ?>" />
					<input type="submit" name="sub_type" value="Yes" />
					<input type="submit" value="No" />
				</form><hr width="500"><br />
			<?php
			}
			else {
				echo "ERROR: No user was selected to delete.<br /><br />";
			}
		}
        
        elseif ($u_action == "cxeesto") { // Show status update form
            
			if ($choosen != NULL AND $choosen != "")
            {
                // Form to edit Cxeesto status.
                $stmt = 'SELECT * FROM `presence` WHERE `uid` = :userid';
                $params = array(
                    'userid' => $choosen
                );
                $row = $conn->queryDB($stmt, $params);
                $row = $row[0];
                ?>
                
                <div id="editform">
                <h2>Edit User Status:</h2>
                <form name="edit_form" method="post">
                    <table>
                        <tr><td>User ID:</td><td><input type="text" name="status_id" value="<?php echo $row['uid']; ?>" autocomplete="off" readonly /></td></tr>
                        <tr><td>Name:</td><td><input type="text" name="status_name" value="<?php echo $row['realname']; ?>" autocomplete="off" readonly /></td></tr>
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
                </form></div><br /> <?php
                
                $showList = false;
			}
			else {
				echo "ERROR: No user was selected to edit &#264;eesto status.<br /><br />";
			}
		}
        
		elseif ($u_action == "edit") { // Show edit user form
		
			// Form to edit user information
                if ($choosen != NULL AND $choosen != "") { ?>
                <div id="editform">
                <h2>Edit User Information:</h2>
                <form name="edit_form" method="post">
                    <table>
                        <tr><td>User ID:</td><td><input type="text" name="edit_uid" value="<?php echo $edit_user_info['userid']; ?>" readonly /></td></tr>
                        <tr><td>Real Name:</td><td><input type="text" name="edit_real" value="<?php echo $edit_user_info['realname']; ?>" autocomplete="off" /></td></tr>
                        <tr><td>Settings ID:</td><td><input type="text" name="edit_sid" value="<?php echo $edit_user_info['settings_id']; ?>" autocomplete="off" /></td></tr>
                        <tr><td>Role:</td><td>
                            <select name="edit_role">
                                <option value="user" <?php echo $edit_user_info['role'] == 'user' ? ' selected' : '';?>>User</option>
                                <option value="guest" <?php echo $edit_user_info['role'] == 'guest' ? ' selected' : '';?>>Guest</option>
                                <option value="admin" <?php echo $edit_user_info['role'] == 'admin' ? ' selected' : '';?>>Admin</option>
                            </select>
                        </td></tr>
                        <tr><td>Theme:</td><td>
				        	<?php
				        		$handle = opendir('themes');
				        		
					        	echo '<select name="userTheme">';
					        		while (false !== ($themeName = readdir($handle))) {
										if ($themeName != '.' && $themeName != '..') {
											if ($themeName == $edit_user_info['theme']) {
												echo '<option value="'.$themeName.'" selected>'.$themeName.'</option>';
											}
											else {
												echo '<option value="'.$themeName.'">'.$themeName.'</option>';
											}
										}
									}
					        	echo '</select>';
				        	?>
			        	</td></tr>
                        <tr><td>Date Created:</td><td><input type="text" name="edit_date" value="<?php echo $edit_user_info['datecreated']; ?>" readonly /></td></tr>
                        <tr><td>First Login:</td><td><input type="text" name="edit_first" value="<?php echo $edit_user_info['firsttime']; ?>" autocomplete="off" /></td></tr>
                    </table>
                    <input type="submit" name="sub_type" value="Save Edit" />
                    <input type="submit" name="sub_type" value="Cancel" />
                </form></div><br />
                <?php
                
                $showList = false;
			}
			else {
				echo "ERROR: No user was selected to edit.<br /><br />";
			}
		}
        
		elseif ($u_action == "add") { // Show create user form
		
			// Form to add a new user
			?>
			<div id="editform">
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
            
            $showList = false;
		}
        
		elseif ($u_action == "reset") { // Show password reset form
		
			// Form to reset user's password
			if ($choosen != NULL AND $choosen != "") { ?>
                <div id="editform">
                <h2>Reset Password for <?php echo $edit_user_info['realname'] ?>:</h2>
                <form name="edit_form" method="post">
                    <table>
                        <tr><td>User ID:</td><td><input type="text" name="reset_uid" value="<?php echo $edit_user_info['userid']; ?>" readonly /></td></tr>
                        <tr><td>Username:</td><td><input type="text" value="<?php echo $edit_user_info['username']; ?>" readonly /></td></tr>
                        <tr><td>New Password:</td><td><input type="password" name="reset_1" /></td></tr>
                        <tr><td>Repeat Password:</td><td><input type="password" name="reset_2" /></td></tr>
                    </table>
                    <input type="submit" name="sub_type" value="Reset" />
                    <input type="submit" name="sub_type" value="Cancel" />
                </form></div><br />
                <?php
            
                $showList = false;
			}
			else {
				echo "ERROR: No user was selected to reset password.<br /><br />";
			}
		}
		
        //-----END FIRST LEVEL ACTIONS-------//
        //-----SECOND LEVEL ACTIONS-------//
        
		if ($sub_typee == "Save Edit") { // Edit user data
			// Edit selected users information
            $stmt = 'UPDATE `users` SET `realname` = :realname, `settings_id` = :s_id, `role` = :role, `firsttime` = :first, `theme` = :theme WHERE `userid` = :userid';
            $params = array(
                'realname' => $_POST['edit_real'],
                's_id' => $_POST['edit_sid'],
                'role' => $_POST['edit_role'],
                'first' => $_POST['edit_first'],
                'userid' => $_POST['edit_uid'],
				'theme' => $_POST['userTheme']
            );
            
            $conn->queryDB($stmt, $params);
            
            $stmt = 'UPDATE `presence` SET `realname` = :realname WHERE `uid` = :userid';
            $params = array(
                'realname' => $_POST['edit_real'],
                'userid' => $_POST['edit_uid']
            );
            
            $conn->queryDB($stmt, $params);
        
            echo 'User Updated<br /><br />';
		}
        
		elseif ($sub_typee == "Add") { // Create new user
            // Check if username already exists            
            $stmt = 'SELECT * FROM `users` WHERE `username` = :username';
            $params = array(
                'username' => $_POST['add_user']
            );
            $row = $conn->queryDB($stmt, $params);
            
            if ($row == NULL) {
                //Add new user to DB
                $date = new DateTime();
                //$date = $date->format('Y-m-d H:i:s');
                
                $add_user = $_POST['add_user'];
                $add_pass = password_hash($_POST['add_pass'], PASSWORD_BCRYPT);
                $add_real = $_POST['add_real'];
                $add_sid = $_POST['add_sid'];
                $add_role = $_POST['add_role'];
                
                // Create user in database
                $stmt = 'INSERT INTO users (username, password, realname, settings_id, role, datecreated, theme) VALUES (:username, :password, :realname, :s_id, :role, :datecreated, \'default\')';
                $params = array(
                    'username' => $add_user,
                    'password' => $add_pass,
                    'realname' => $add_real,
                    's_id' => $add_sid,
                    'role' => $add_role,
                    'datecreated' => $date->format('Y-m-d')
                );                
                $conn->queryDB($stmt, $params);
                
                // Get the ID of the new user
                $stmt = 'SELECT `userid` FROM users WHERE username = :user';
                $params = array(
                    'user' => $add_user
                );        
                $row = $conn->queryDB($stmt, $params);;
                
                // Create a Cxeesto ID for the new user
                $stmt = 'INSERT INTO `presence` (`uid`, `realname`, `status`, `message`, `return`, `dmodified`) VALUES (:uid, :real, 1, \'\', \'00:00:00\', :date)';
                $params = array(
                    'uid' => $row[0]['userid'],
                    'real' => $add_real,
                    'date' => $date->format('Y-m-d H:i:s')
                );                
                $conn->queryDB($stmt, $params);
                
                echo 'User Added<br /><br />';
            }

            else {
                echo 'Username already exists!';
            }
		}
        
		elseif ($sub_typee == "Reset") { // Reset user password
			$reset_3 = $_POST['reset_1'];
			$reset_4 = $_POST['reset_2'];
			
			if ($reset_3 == $reset_4) {
				$reset_3 = password_hash($reset_3, PASSWORD_BCRYPT); // Hash the password if they match

                // Update record with new password
                $stmt = 'UPDATE `users` SET `password` = :newpass WHERE `userid` = :myID';
                $params = array(
                    'newpass' => $reset_3,
                    'myID' => $_POST['reset_uid']
                );
                $conn->queryDB($stmt, $params);
                    
                echo 'Password change successful.<br /><br />';
			}
			else {
				echo 'New passwords do not match<br /><br />';
			}
		}
        
		elseif ($sub_typee == "Yes") { // Delete user

            $stmt = 'DELETE FROM `users` WHERE `userid` = :userid';
            $stmt2 = 'DELETE FROM `presence` WHERE `uid` = :userid';
            $params = array(
                'userid' => $choosen
            );
            
            $conn->queryDB($stmt, $params);
            $conn->queryDB($stmt2, $params);
            
            echo "Action Taken: User Deleted<br /><br />";
		}
        
        elseif ($sub_typee == "Set Status") { // Change user Cxeesto status
            $date = new DateTime();
            $date = $date->format('Y-m-d H:i:s');
            
            $stat_id = $_POST['status_id'];
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
            
            $stmt = 'UPDATE `presence` SET `message` = :message, `status` = :status, `return` = :return, `dmodified` = :date WHERE `uid` = :userid';
            $params = array(
                'message' => $stat_message,
                'status' => $stat_s,
                'return' => $stat_return,
                'date' => $date,
                'userid' => $stat_id
            );
            $conn->queryDB($stmt, $params);
        
            echo 'User Status Updated<br /><br />';
		}
        
        //-----END SECOND LEVEL ACTIONS-------//
	}