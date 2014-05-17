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
namespace Dandelion;
use Dandelion\Database\dbManage;

$conn = new dbManage();

if (isset($_POST['user_action']) || isset($_POST['sub_type'])) {
    if (isset($_POST['user_action'])) {
        $u_action  = $_POST['user_action'];
        $u_action2 = isset($_POST['user_action2']) ? $_POST['user_action2'] : 'none';
        $choosen   = isset($_POST['the_choosen_one']) ? $_POST['the_choosen_one'] : '';

        if ($u_action == 'none' && $u_action2 != 'none') {
            $u_action = $u_action2;
        } elseif ($u_action != 'none' && $u_action2 != 'none') {
            $u_action = 'none';
            echo 'ERROR: Both action boxes had a selection.<br><br>';
        } elseif ($u_action == 'none' && $u_action2 == 'none') {
            echo 'ERROR: No action selected.<br><br>';
        }

        $stmt = 'SELECT * FROM `'.DB_PREFIX.'users` WHERE `userid` = :userid';
        $params = array(
                'userid' => $choosen
        );
        $edit_user_info = $conn->queryDB($stmt, $params);
        $edit_user_info = isset($edit_user_info[0]) ? $edit_user_info[0] : '';

        require_once ROOT.'/classes/usersForms.php';
        $userforms = new UserForms();

        if ($u_action != 'add' && !empty($choosen) && $edit_user_info !== '') {

            if ($u_action == 'delete' && ($_SESSION['rights']['deleteuser'] || $_SESSION['rights']['admin'])) { // Confirm user delete
                $userforms->confirmDelete($edit_user_info['realname'], $choosen);
            } elseif ($u_action == 'cxeesto' && ($_SESSION['rights']['edituser'] || $_SESSION['rights']['admin'])) { // Show status update form
                $stmt = 'SELECT * FROM `'.DB_PREFIX.'presence` WHERE `uid` = :userid';
                $params = array(
                        'userid' => $choosen
                );
                $row = $conn->queryDB($stmt, $params);

                if (!empty($row)) {
                    $row = $row[0];
                    $userforms->editCxeesto($row);
                } else {
                    echo 'ERROR: Selected user doesn\'t have a &#264;eesto account.<br><br>';
                }
            } elseif ($u_action == 'edit' && ($_SESSION['rights']['edituser'] || $_SESSION['rights']['admin'])) { // Show edit user form
                $userforms->editUser($edit_user_info);
                $showList = false;
            } elseif ($u_action == 'reset' && ($_SESSION['rights']['edituser'] || $_SESSION['rights']['admin'])) { // Show password reset form
                $userforms->resetPassword($choosen, $edit_user_info['username'], $edit_user_info['realname']);
                $showList = false;
            }
        } elseif ($u_action == 'add' && ($_SESSION['rights']['adduser'] || $_SESSION['rights']['admin'])) { // Show create user form
            $userforms->addUser();
            $showList = false;
        } elseif ($u_action != 'none' && empty($choosen)) {
            echo 'ERROR: No user was selected.<br><br>';
        } elseif ($edit_user_info === '') {
            echo 'Error getting information from database.<br><br>';
        }
    }

    if (isset($_POST['sub_type'])) {
        $choosen   = isset($_POST['the_choosen_one']) ? $_POST['the_choosen_one'] : '';
        $second_tier = $_POST['sub_type'];

        require_once ROOT.'/classes/users.php';
        $useractions = new User();

        if ($second_tier == "Save Edit" && ($_SESSION['rights']['edituser'] || $_SESSION['rights']['admin'])) { // Edit user data
            $edit = array(
                'realname' => $_POST['edit_real'],
                'role' => $_POST['edit_role'],
                'first' => $_POST['edit_first'],
                'uid' => $_POST['edit_uid'],
                'theme' => $_POST['userTheme']
            );

            echo $useractions->editUser($edit);
        } elseif ($second_tier == "Add" && ($_SESSION['rights']['adduser'] || $_SESSION['rights']['admin'])) { // Create new user
            $add = array(
                'username' => $_POST['add_user'],
                'password' => $_POST['add_pass'],
                'realname' => $_POST['add_real'],
                'role' => $_POST['add_role']
            );

            echo $useractions->addUser($add);
        } elseif ($second_tier == "Reset" && ($_SESSION['rights']['edituser'] || $_SESSION['rights']['admin'])) { // Reset user password
            $reset_3 = $_POST['reset_1'];
            $reset_4 = $_POST['reset_2'];

            if ($reset_3 == $reset_4) {
                echo $useractions->resetUserPw($_POST['reset_uid'], $reset_3);
            } else {
                echo 'New passwords do not match<br><br>';
            }
        } elseif ($second_tier == "Yes" && ($_SESSION['rights']['deleteuser'] || $_SESSION['rights']['admin'])) { // Delete user
            echo empty($choosen) ? 'Delete failed, no user selected.' : $useractions->deleteUser($choosen);
        } elseif ($second_tier == "Set Status" && ($_SESSION['rights']['edituser'] || $_SESSION['rights']['admin'])) { // Change user Cxeesto status
            $date = new \DateTime();
            $date = $date->format('Y-m-d H:i:s');

            $user_id = $_POST['status_id'];
            $status = $_POST['status_s'];
            $message = $_POST['status_message'];
            $return = $_POST['status_return'];

            echo $useractions->updateUserStatus($user_id, $status, $message, $return);
        }
    }
}
