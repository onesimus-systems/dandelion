<?php
/**
  * @brief Shows forms for user management
  *
  * @author Lee Keitel
  * @date March, 2014
***/
class UserForms
{
	if ($u_action == "delete") { // Confirm user delete
		
		// Delete selected user from DB
		if ($choosen != NULL AND $choosen != "") {
		?>
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
	
		if ($choosen != NULL AND $choosen != "") {
			//Form to edit Cxeesto status.
			$stmt = 'SELECT * FROM `presence` WHERE `uid` = :userid';
			$params = array(
				'userid' => $choosen
			);
			$row = $conn->queryDB($stmt, $params);
			
			if (!empty($row)) {
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
					</form>
				</div><br />
				<?php
				$showList = false;
			}
			else {
				echo "ERROR: Selected user doesn't have a &#264;eesto account.<br /><br />";
			}
		}
		else {
			echo "ERROR: No user was selected to edit &#264;eesto status.<br /><br />";
		}
	}
	        
	elseif ($u_action == "edit") { // Show edit user form
		// Form to edit user information
		if ($choosen != NULL AND $choosen != "") {
			?>
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
							<?php getThemeList($edit_user_info['theme']); ?>
						</td></tr>
						<tr><td>Date Created:</td><td><input type="text" name="edit_date" value="<?php echo $edit_user_info['datecreated']; ?>" readonly /></td></tr>
						<tr><td>First Login:</td><td><input type="text" name="edit_first" value="<?php echo $edit_user_info['firsttime']; ?>" autocomplete="off" /></td></tr>
					</table>
					<input type="submit" name="sub_type" value="Save Edit" />
					<input type="submit" name="sub_type" value="Cancel" />
				</form>
			</div><br />
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
				</form>
		</div><br />
		<?php
		$showList = false;
	}
	        
	elseif ($u_action == "reset") { // Show password reset form
		// Form to reset user's password
		if ($choosen != NULL AND $choosen != "") {
			?>
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
				</form>
			</div><br />
			<?php
			$showList = false;
		}
		else {
			echo "ERROR: No user was selected to reset password.<br /><br />";
		}
	}
}