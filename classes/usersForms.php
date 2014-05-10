<?php
/**
  * @brief Shows forms for user management
  *
  * @author Lee Keitel
  * @date March, 2014
  * 
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
class UserForms
{	
	/** Confirm user delete form
	 *
	 * @param name (string) - User's real name
	 * @param uid (int) - User's ID number
	 *
	 * @return nothing
	 */
	public function confirmDelete($name, $uid) {
		if ($uid != $_SESSION['userInfo']['userid']) {
    	?>
    		<br /><hr width="500">
    		Are you sure you want to delete "<?php echo $name; ?>"?<br /><br />
    		<form method="post">
    			<input type="hidden" name="the_choosen_one" value="<?php echo $uid; ?>" />
    			<input type="submit" name="sub_type" value="Yes" />
    			<input type="submit" value="No" />
    		</form>
    		<hr width="500"><br />
    	<?php
		}
		else {
			echo '<br>You can\'t delete yourself.<br><br>';
		}
	}

	/** Edit user status form
	 *
	 * @param row (associative array) - All user information from database for Cxeesto
	 *
	 * @return nothing
	 */
	public function editCxeesto($row) {
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
					<tr><td>Return:</td><td><input type="text" name="status_return" id="datepick" value="<?php echo $row['return']; ?>" /></td></tr>
				</table>
				<input type="submit" name="sub_type" value="Set Status" />
				<input type="submit" name="sub_type" value="Cancel" />
			</form>

			<script type="text/javascript">
            $(document).ready(function() {
                $('#datepick').datetimepicker({
                        timeFormat: "HH:mm",
                        controlType: 'select',
                        stepMinute: 10,
                    });
            	});
			</script>
		</div><br />
	<?php
	}

	/** Edit user form
	 *
	 * @param userInfo (keyed array) - All user information from database
	 *
	 * @return nothing
	 */
	public function editUser($userInfo) {
	?>
		<div id="editform">
			<h2>Edit User Information:</h2>
			<form name="edit_form" method="post">
				<table>
					<tr><td>User ID:</td><td><input type="text" name="edit_uid" value="<?php echo $userInfo['userid']; ?>" readonly></td></tr>
					<tr><td>Real Name:</td><td><input type="text" name="edit_real" value="<?php echo $userInfo['realname']; ?>" autocomplete="off"></td></tr>
					<tr><td>Role:</td><td>
					<?php
                        $permissions = new Permissions();
                        $list = $permissions->getGroupList();
                        
                        echo '<select name="edit_role">';
                        foreach ($list as $group) {
                            echo '<option value="'.$group['role'].'">'.ucfirst($group['role']).'</option>';
                        }
                        echo '</select>';
						?>
					</td></tr>
					<tr><td>Theme:</td><td>
						<?php getThemeList($userInfo['theme']); ?>
					</td></tr>
					<tr><td>Date Created:</td><td><input type="text" name="edit_date" value="<?php echo $userInfo['datecreated']; ?>" readonly></td></tr>
					<tr><td>First Login:</td><td><input type="text" name="edit_first" value="<?php echo $userInfo['firsttime']; ?>" autocomplete="off"></td></tr>
				</table>
				<input type="submit" name="sub_type" value="Save Edit">
				<input type="submit" name="sub_type" value="Cancel">
			</form>
		</div><br>
	<?php
	}

	/** Add new user form
	 *
	 * @return nothing
	 */
	public function addUser() {
	?>
		<div id="editform">
			<h2>Add a User:</h2>
				<form name="edit_form" method="post">
					<table>
						<tr><td>Username:</td><td><input type="text" name="add_user" autocomplete="off" /></td></tr>
						<tr><td>Password:</td><td><input type="password" name="add_pass" /></td></tr>
						<tr><td>Real Name:</td><td><input type="text" name="add_real" autocomplete="off" /></td></tr>
						<tr><td>Role:</td><td>
						<?php
                        $permissions = new Permissions();
                        $list = $permissions->getGroupList();
                        
                        echo '<select name="add_role">';
                        foreach ($list as $group) {
                            echo '<option value="'.$group['role'].'">'.ucfirst($group['role']).'</option>';
                        }
                        echo '</select>';
						?>
						</td></tr>
					</table>
					<input type="submit" name="sub_type" value="Add">
					<input type="submit" name="sub_type" value="Cancel">
				</form>
		</div><br>
	<?php
	}

	/** Confirm user delete form
	 *
	 * @param uid (int) - User's ID number
	 * @param uname (string) - User's username
	 * @param realname (string) - User's name
	 *
	 * @return nothing
	 */
	public function resetPassword($uid, $uname, $realname) {
	?>
		<div id="editform">
			<h2>Reset Password for <?php echo $realname; ?>:</h2>
			<form name="edit_form" method="post">
				<table>
					<tr><td>User ID:</td><td><input type="text" name="reset_uid" value="<?php echo $uid; ?>" readonly /></td></tr>
					<tr><td>Username:</td><td><input type="text" value="<?php echo $uname; ?>" readonly /></td></tr>
					<tr><td>New Password:</td><td><input type="password" name="reset_1" /></td></tr>
					<tr><td>Repeat Password:</td><td><input type="password" name="reset_2" /></td></tr>
				</table>
				<input type="submit" name="sub_type" value="Reset" />
				<input type="submit" name="sub_type" value="Cancel" />
			</form>
		</div><br />
	<?php
	}
}