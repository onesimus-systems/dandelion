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
namespace Dandelion\API\Module;

use Dandelion\API\ApiController;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(ApiController::makeDAPI(2, 'This script can only be called by the API.', 'users'));
}

class usersAPI
{
    public static function resetPassword($db, $ur, $params) {
        $userid = USER_ID;

        // Check permissions
        if (isset($params->uid)) {
            if ($ur->authorized('edituser') || $params->uid == USER_ID) {
                $userid = $params->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
            }
        }

        // Validate password
        $newPass = $params->pw;
        if ($newPass == '' || $newPass == null) {
            exit(ApiController::makeDAPI(5, 'New password cannot be empty', 'users'));
            return;
        }

        // Do action
        $user = new \Dandelion\Users($db, $userid);
        return json_encode($user->resetPassword($newPass));
    }

    public static function create($db, $ur, $params) {
        if (!$ur->authorized('adduser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $username = $params->username;
        $password = $params->password;
        $realname = $params->fullname;
        $role     = $params->role;
        //$cheesto = $params->makecheesto;

        $user = new \Dandelion\Users($db);
        return json_encode($user->createUser($username, $password, $realname, $role));
    }

    public static function save($db, $ur, $params) {
        if (!$ur->authorized('edituser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $uid = $params->uid;
        if (empty($uid)) {
            exit(ApiController::makeDAPI(5, 'No user id received.', 'users'));
        }

        $user = new \Dandelion\Users($db, $uid, true);
        $user->userInfo['realname']  = $params->get('fullname', $user->userInfo['realname']);
        $user->userInfo['role']  = $params->get('role', $user->userInfo['role']);
        $user->userInfo['firsttime']  = $params->get('prompt', $user->userInfo['firsttime']);
        $user->userInfo['theme']  = $params->get('theme', $user->userInfo['theme']);
        return json_encode($user->saveUser());
    }

    public static function delete($db, $ur, $params) {
        if (USER_ID == $params->uid) {
            exit(ApiController::makeDAPI(5, 'You can\'t delete yourself.', 'users'));
        }

        // Check permissions
        if ($ur->authorized('edituser')) {
            $userid = $params->uid;
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $user = new \Dandelion\Users($db);
        return json_encode($user->deleteUser($userid));
    }

    public static function getUsersList($db, $ur, $params) {
        // Check permissions
        if ($ur->authorized('edituser')) {
            $userid = $params->uid;
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }
        $list = new \Dandelion\Users($db);
        return json_encode($list->getUserList());
    }

    public static function getUserInfo($db, $ur, $params) {
        // Check permissions
        if ($ur->authorized('edituser')) {
            $userid = $params->uid;
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $uid = $params->uid;
        if (empty($uid)) {
            exit(ApiController::makeDAPI(5, 'No user id received.', 'users'));
        }

        $user = new \Dandelion\Users($db);
        return json_encode($user->getUser($uid));
    }
}
