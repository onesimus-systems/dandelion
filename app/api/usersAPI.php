<?php
/**
 * Handles API requests for user management
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date January 2015
 */
namespace Dandelion\API;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(ApiController::makeDAPI(2, 'This script can only be called by the API.', 'users'));
}

class usersAPI
{
    public static function resetPassword($db, $ur) {
        $userid = USER_ID;

        // Check permissions
        if (isset($_REQUEST['uid'])) {
            if ($ur->authorized('edituser') || $_REQUEST['uid'] == USER_ID) {
                $userid = $_REQUEST['uid'];
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
            }
        }

        // Validate password
        $newPass = $_REQUEST['pw'];
        if ($newPass == '' || $newPass == null) {
            exit(ApiController::makeDAPI(5, 'New password cannot be empty', 'users'));
            return;
        }

        // Do action
        $user = new \Dandelion\Users($db, $userid);
        return json_encode($user->resetPassword($newPass));
    }

    public static function create($db, $ur) {
        if (!$ur->authorized('adduser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        $realname = $_REQUEST['fullname'];
        $role     = $_REQUEST['role'];
        //$cheesto = $_REQUEST['makecheesto'];

        $user = new \Dandelion\Users($db);
        return json_encode($user->createUser($username, $password, $realname, $role));
    }

    public static function save($db, $ur) {
        if (!$ur->authorized('edituser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $uid = $_REQUEST['uid'];
        if (empty($uid)) {
            exit(ApiController::makeDAPI(5, 'No user id received.', 'users'));
        }

        $user = new \Dandelion\Users($db, $uid, true);
        $user->userInfo['realname']  = isset($_REQUEST['fullname']) ? $_REQUEST['fullname'] : $user->userInfo['realname'];
        $user->userInfo['role']  = isset($_REQUEST['role']) ? $_REQUEST['role'] : $user->userInfo['role'];
        $user->userInfo['firsttime']  = isset($_REQUEST['prompt']) ? $_REQUEST['prompt'] : $user->userInfo['firsttime'];
        $user->userInfo['theme']  = isset($_REQUEST['theme']) ? $_REQUEST['theme'] : $user->userInfo['theme'];
        return json_encode($user->saveUser());
    }

    public static function delete($db, $ur) {
        if (USER_ID == $_REQUEST['uid']) {
            exit(ApiController::makeDAPI(5, 'You can\'t delete yourself.', 'users'));
        }

        // Check permissions
        if ($ur->authorized('edituser')) {
            $userid = $_REQUEST['uid'];
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $user = new \Dandelion\Users($db);
        return json_encode($user->deleteUser($userid));
    }

    public static function getUsersList($db, $ur) {
        // Check permissions
        if ($ur->authorized('edituser')) {
            $userid = $_REQUEST['uid'];
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }
        $list = new \Dandelion\Users($db);
        return json_encode($list->getUserList());
    }

    public static function getUserInfo($db, $ur) {
        // Check permissions
        if ($ur->authorized('edituser')) {
            $userid = $_REQUEST['uid'];
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $uid = $_REQUEST['uid'];
        if (empty($uid)) {
            exit(ApiController::makeDAPI(5, 'No user id received.', 'users'));
        }

        $user = new \Dandelion\Users($db);
        return json_encode($user->getUser($uid));
    }
}
