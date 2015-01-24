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

use Dandelion\Controllers\ApiController;

class usersAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    public function resetPassword() {
        $userid = USER_ID;

        // Check permissions
        if (isset($this->up->uid)) {
            if ($this->ur->authorized('edituser') || $this->up->uid == USER_ID) {
                $userid = $this->up->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
            }
        }

        // Validate password
        $newPass = $this->up->pw;
        if ($newPass == '' || $newPass == null) {
            exit(ApiController::makeDAPI(5, 'New password cannot be empty', 'users'));
            return;
        }

        // Do action
        $user = new \Dandelion\Users($this->db, $userid);
        return json_encode($user->resetPassword($newPass));
    }

    public function create() {
        if (!$this->ur->authorized('adduser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $username = $this->up->username;
        $password = $this->up->password;
        $realname = $this->up->fullname;
        $role     = $this->up->role;
        //$cheesto = $this->up->makecheesto;

        $user = new \Dandelion\Users($this->db);
        return json_encode($user->createUser($username, $password, $realname, $role));
    }

    public function save() {
        if (!$this->ur->authorized('edituser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $uid = $this->up->uid;
        if (empty($uid)) {
            exit(ApiController::makeDAPI(5, 'No user id received.', 'users'));
        }

        $user = new \Dandelion\Users($this->db, $uid, true);
        $user->userInfo['realname']  = $this->up->get('fullname', $user->userInfo['realname']);
        $user->userInfo['role']  = $this->up->get('role', $user->userInfo['role']);
        $user->userInfo['firsttime']  = $this->up->get('prompt', $user->userInfo['firsttime']);
        $user->userInfo['theme']  = $this->up->get('theme', $user->userInfo['theme']);
        return json_encode($user->saveUser());
    }

    public function delete() {
        if (USER_ID == $this->up->uid) {
            exit(ApiController::makeDAPI(5, 'You can\'t delete yourself.', 'users'));
        }

        // Check permissions
        if ($this->ur->authorized('edituser')) {
            $userid = $this->up->uid;
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $user = new \Dandelion\Users($this->db);
        return json_encode($user->deleteUser($userid));
    }

    public function getUsersList() {
        // Check permissions
        if ($this->ur->authorized('edituser')) {
            $userid = $this->up->uid;
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }
        $list = new \Dandelion\Users($this->db);
        return json_encode($list->getUserList());
    }

    public function getUserInfo() {
        // Check permissions
        if ($this->ur->authorized('edituser')) {
            $userid = $this->up->uid;
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $uid = $this->up->uid;
        if (empty($uid)) {
            exit(ApiController::makeDAPI(5, 'No user id received.', 'users'));
        }

        $user = new \Dandelion\Users($this->db);
        return json_encode($user->getUser($uid));
    }
}
