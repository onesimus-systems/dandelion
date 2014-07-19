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
 */
namespace Dandelion;

use Dandelion\Database\dbManage;
use Dandelion\Users\UserForms;
use Dandelion\Users\User;

$conn = new dbManage();

if (isset($_POST['user_action'])) {
    $u_action = $_POST['user_action'];
    $userId = isset($_POST['the_choosen_one']) ? $_POST['the_choosen_one'] : '';
    
    $user = new User(true, $userId);
    
    if ($u_action != 'add' && !empty($userId)) {
        if ($u_action == 'delete' && $User_Rights->authorized('deleteuser')) { // Confirm user delete
            userforms::confirmDelete($user);
        }
        elseif ($u_action == 'cxeesto' && $User_Rights->authorized('edituser')) { // Show status update form
            if (!empty($user->userCheesto)) {
                userforms::editCxeesto($user);
            }
            else {
                echo 'ERROR: Selected user doesn\'t have a &#264;eesto account.<br><br>';
            }
            
            $showList = false;
        }
        elseif ($u_action == 'edit' && $User_Rights->authorized('edituser')) { // Show edit user form
            userforms::editUser($user);
            $showList = false;
        }
        elseif ($u_action == 'reset' && $User_Rights->authorized('edituser')) { // Show password reset form
            userforms::resetPassword($user);
            $showList = false;
        }
        elseif ($u_action == 'revokeKey' && $User_Rights->authorized('edituser')) { // Confirm API revoke
            userforms::confirmKeyRevoke($user);
        }
    }
    elseif ($u_action == 'add' && $User_Rights->authorized('adduser')) { // Show create user form
        userforms::addUser();
        $showList = false;
    }
    elseif ($u_action != 'none' && empty($userId)) {
        echo 'ERROR: No user was selected.<br><br>';
    }
}

if (isset($_POST['sub_type'])) {
    $userId = isset($_POST['the_choosen_one']) ? $_POST['the_choosen_one'] : '';
    $second_tier = $_POST['sub_type'];
    
    if ($second_tier == "Save Edit" && $User_Rights->authorized('edituser')) { // Edit user data
        $user = new User();
        $user->userInfo = array(
            'realname' => $_POST['edit_real'],
            'role' => $_POST['edit_role'],
            'firsttime' => $_POST['edit_first'],
            'theme' => $_POST['userTheme'],
            'userid' => $_POST['edit_uid'] 
        );
        
        echo $user->editUser();
    }
    elseif ($second_tier == "Add" && $User_Rights->authorized('adduser')) { // Create new user
        $user = new User();
        $user->userInfo = array(
            'username' => $_POST['add_user'],
            'password' => $_POST['add_pass'],
            'realname' => $_POST['add_real'],
            'role' => $_POST['add_role'] 
        );
        $user->userCheesto['create'] = true;
        
        echo $user->addUser();
    }
    elseif ($second_tier == "Reset" && $User_Rights->authorized('edituser')) { // Reset user password
        $reset_3 = $_POST['reset_1'];
        $reset_4 = $_POST['reset_2'];
        
        if ($reset_3 == $reset_4) {
            $user = new User(false, $_POST['reset_uid']);
            $user->userInfo['password'] = $reset_3;
            echo $user->resetUserPw();
        }
        else {
            echo 'New passwords do not match<br><br>';
        }
    }
    elseif ($second_tier == "Yes" && $User_Rights->authorized('deleteuser')) { // Delete user
        if (!empty($userId)) {
            $user = new User(false, $userId);
            echo $user->deleteUser();
        }
        else {
            echo 'Delete failed, no user selected.';
        }
    }
    elseif ($second_tier == "Revoke" && $User_Rights->authorized('edituser')) { // Revoke API keys
        if (!empty($userId)) {
            $user = new User(false, $userId);
            echo $user->revokeAPIKey();
        }
        else {
            echo 'API revoke failed, no user selected.';
        }
    }
    elseif ($second_tier == "Set Status" && $User_Rights->authorized('edituser')) { // Change user Cxeesto status
        $user_id = $_POST['status_id'];
        
        $user = new User(false, $_POST['status_id']);
        
        $user->userCheesto = array(
            'status' => $_POST['status_s'],
            'message' => $_POST['status_message'],
            'returntime' => $_POST['status_return'] 
        );
        
        echo $user->updateUserStatus();
    }
}
