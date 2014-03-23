<?php
/**
 * This script manages all admin user related actions.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

// Connect to DB
$conn = new dbManage();

$u_action  = isset($_POST['user_action']) ? $_POST['user_action'] : '';
$u_action2 = isset($_POST['user_action2']) ? $_POST['user_action2'] : '';
$choosen   = isset($_POST['the_choosen_one']) ? $_POST['the_choosen_one'] : '';
$second_tier = isset($_POST['sub_type']) ? $_POST['sub_type'] : '';

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

if ($second_tier != '') {
	require_once ROOT.'/classes/users.php';
	$useractions = new User($conn);
}

if ($u_action != '') {
	require_once ROOT.'/classes/usersForms.php';
	$userforms = new UserForms();
}

//-----FIRST LEVEL ACTIONS-------//

if ($u_action == "delete") { // Confirm user delete

	// Delete selected user from DB
	if ($choosen != NULL AND $choosen != "") {
		$userforms->confirmDelete($edit_user_info['realname'], $choosen);
	}
	else {
		echo "ERROR: No user was selected to delete.<br /><br />";
	}
}
        
elseif ($u_action == "cxeesto") { // Show status update form

	if ($choosen != NULL AND $choosen != "") {
		$stmt = 'SELECT * FROM `presence` WHERE `uid` = :userid';
		$params = array(
				'userid' => $choosen
		);
		$row = $conn->queryDB($stmt, $params);
		
		if (!empty($row)) {
			$row = $row[0];
			$userforms->editCxeesto($row);
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
	if ($choosen != NULL AND $choosen != "") {
		$userforms->editUser($edit_user_info);
		$showList = false;
	}
	else {
		echo "ERROR: No user was selected to edit.<br /><br />";
	}
}
        
elseif ($u_action == "add") { // Show create user form		
	$userforms->addUser();
	$showList = false;
}
        
elseif ($u_action == "reset") { // Show password reset form
	// Form to reset user's password
	if ($choosen != NULL AND $choosen != "") {
		$userforms->resetPassword($choosen, $edit_user_info['username'], $edit_user_info['realname']);
		$showList = false;
	}
	else {
		echo "ERROR: No user was selected to reset password.<br /><br />";
	}
}

//-----END FIRST LEVEL ACTIONS-------//
//-----SECOND LEVEL ACTIONS-------//
        
if ($second_tier == "Save Edit") { // Edit user data
	$edit = array(
		'realname' => $_POST['edit_real'],
		'sid' => $_POST['edit_sid'],
		'role' => $_POST['edit_role'],
		'first' => $_POST['edit_first'],
		'uid' => $_POST['edit_uid'],
		'theme' => $_POST['userTheme']
	);
	
	echo $useractions->editUser($edit);
}
        
elseif ($second_tier == "Add") { // Create new user
	$add = array(
		'username' => $_POST['add_user'],
		'password' => $_POST['add_pass'],
		'realname' => $_POST['add_real'],
		'sid' => $_POST['add_sid'],
		'role' => $_POST['add_role']
	);
	
	echo $useractions->addUser($add);
}
        
elseif ($second_tier == "Reset") { // Reset user password
	$reset_3 = $_POST['reset_1'];
	$reset_4 = $_POST['reset_2'];
			
	if ($reset_3 == $reset_4) {
		echo $useractions->resetUserPw($_POST['reset_uid'], $reset_3);
	}
	else {
		echo 'New passwords do not match<br /><br />';
	}
}
        
elseif ($second_tier == "Yes") { // Delete user
	echo $useractions->deleteUser($choosen);
}
        
elseif ($second_tier == "Set Status") { // Change user Cxeesto status
	$date = new DateTime();
	$date = $date->format('Y-m-d H:i:s');
	
	$user_id = $_POST['status_id'];
	$status = $_POST['status_s'];
	$message = $_POST['status_message'];
	$return = $_POST['status_return'];
	
	echo $useractions->updateUserStatus($user_id, $status, $message, $return);
}
//-----END SECOND LEVEL ACTIONS-------//