<?php

/**
 * Handle user management functions
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
 * @date Feb 2014
 */
namespace Dandelion\Users;

use Dandelion\Database\dbManage;
use Dandelion\Permissions;

class User extends dbManage
{
    // User data
    public $userInfo = array();
    public $userCheesto = array();
    public $userApi = array();

    /**
     * Prepare database connection and load user info if needed
     *
     * @param bool $load - Load user information for user $uid
     * @param int $uid - ID of user to load
     */
    public function __construct($load = false, $uid = -1) {
        // Prepare database connection
        parent::__construct();
        
        if ($uid >= 0) {
            $this->userInfo['userid'] = $uid;
        }
        
        if ($load === true && $uid >= 0) {
            $this->loadUser();
        }
        elseif ($load === true && $uid < 0) {
            echo 'To load a user you must provide a user ID.';
        }
        
        return true;
    }

    /**
     * Get data from database for use from the users, presence, and api tables
     */
    public function loadUser() {
        $sql = 'SELECT  u.userid AS u_userid,
                            u.username AS u_username,
                            u.realname AS u_realname,
                            u.role AS u_role,
                            u.firsttime AS u_firsttime,
                            u.theme AS u_theme,
                            u.datecreated AS u_datecreated,
                        p.id AS p_id,
                            p.status AS p_status,
                            p.message AS p_message,
                            p.returntime AS p_returntime,
                        a.keystring AS a_keystring,
                            a.expires AS a_expires,
                        r.permissions AS u_permissions
                    FROM ' . DB_PREFIX . 'users AS u
                    LEFT JOIN ' . DB_PREFIX . 'presence AS p
                        ON p.uid = u.userid
                    LEFT JOIN ' . DB_PREFIX . 'apikeys AS a
                        ON a.user = u.userid
                    LEFT JOIN ' . DB_PREFIX . 'rights AS r
                        ON r.role = u.role
                    WHERE u.userid = :userid';
        
        $params = array(
            'userid' => $this->userInfo['userid'] 
        );
        
        $allUserInfo = $this->queryDB($sql, $params);
        
        foreach ($allUserInfo[0] as $key => $value) {
            $infoType = substr($key, 0, 2);
            $key = substr_replace($key, '', 0, 2);
            
            switch ($infoType) {
                case 'u_':
                    if ($key == 'permissions') {
                        $value = (array) unserialize($value);
                    }
                    $this->userInfo[$key] = $value;
                    break;
                case 'p_':
                    $this->userCheesto[$key] = $value;
                    break;
                case 'a_':
                    $this->userApi[$key] = $value;
                    break;
            }
        }
        
        return true;
    }

    /**
     * Update user information=
     *
     * @return string - Success message
     */
    public function editUser() {
        if (!empty($this->userInfo['realname']) && !empty($this->userInfo['theme']) && !empty($this->userInfo['role']) && is_numeric($this->userInfo['firsttime']) && !empty($this->userInfo['userid'])) {
            $stmt = 'UPDATE `' . DB_PREFIX . 'users` SET `realname` = :realname, `role` = :role, `firsttime` = :first, `theme` = :theme WHERE `userid` = :userid';
            $params = array(
                'realname' => $this->userInfo['realname'],
                'role' => $this->userInfo['role'],
                'first' => $this->userInfo['firsttime'],
                'userid' => $this->userInfo['userid'],
                'theme' => $this->userInfo['theme'] 
            );
            
            $this->queryDB($stmt, $params);
            
            $stmt = 'UPDATE `' . DB_PREFIX . 'presence` SET `realname` = :realname WHERE `uid` = :userid';
            $params = array(
                'realname' => $this->userInfo['realname'],
                'userid' => $this->userInfo['userid'] 
            );
            
            if ($this->queryDB($stmt, $params)) {
                return 'User Updated<br><br>';
            }
            else {
                return 'Error updating user<br><br>';
            }
        }
        else {
            return 'Error 0x1c2u3e';
        }
    }

    /**
     * Create a new user
     *
     * @return string - Success message
     */
    public function addUser() {
        $date = new \DateTime();
        $add_user = $this->userInfo['username'];
        $add_pass = password_hash($this->userInfo['password'], PASSWORD_BCRYPT);
        $add_real = $this->userInfo['realname'];
        $add_role = $this->userInfo['role'];
        
        if (!empty($add_user) && !empty($add_pass) && !empty($add_real) && !empty($add_role)) {
            if (!$this->isUser($add_user)) {
                // Create row in users table
                $stmt = 'INSERT INTO `' . DB_PREFIX . 'users` (username, password, realname, role, datecreated, theme) VALUES (:username, :password, :realname, :role, :datecreated, \'\')';
                $params = array(
                    'username' => $add_user,
                    'password' => $add_pass,
                    'realname' => $add_real,
                    'role' => $add_role,
                    'datecreated' => $date->format('Y-m-d') 
                );
                $this->queryDB($stmt, $params);
                
                if ($this->userCheesto['create'] === true) {
                    $lastID = $this->lastInsertId();
                    
                    // Create row in presence table
                    $stmt = 'INSERT INTO `' . DB_PREFIX . 'presence` (`uid`, `realname`, `status`, `message`, `returntime`, `dmodified`) VALUES (:uid, :real, 1, \'\', \'00:00:00\', :date)';
                    $params = array(
                        'uid' => $lastID,
                        'real' => $add_real,
                        'date' => $date->format('Y-m-d H:i:s') 
                    );
                    
                    $this->queryDB($stmt, $params);
                }
                
                return 'User Added<br><br>';
            }
            else {
                return 'Username already exists!<br><br>';
            }
        }
        else {
            return 'Error 0x1c2u3a';
        }
    }

    /**
     * Checks if username is already taken
     *
     * @param string $username
     */
    private function isUser($username) {
        $stmt = 'SELECT *
                 FROM `' . DB_PREFIX . 'users`
                 WHERE `username` = :username';
        $params = array(
            'username' => $username 
        );
        $row = $this->queryDB($stmt, $params);
        
        if (empty($row))
            return false;
        else
            return true;
    }

    /**
     * Reset user password
     *
     * @return string - Success message
     */
    public function resetUserPw() {
        $uid = $this->userInfo['userid'];
        $pass = $this->userInfo['password'];
        
        if (!empty($uid) && !empty($pass)) {
            $pass = password_hash($pass, PASSWORD_BCRYPT);
            
            $stmt = 'UPDATE `' . DB_PREFIX . 'users` SET `password` = :newpass WHERE `userid` = :id';
            $params = array(
                'newpass' => $pass,
                'id' => $uid 
            );
            
            if ($this->queryDB($stmt, $params)) {
                return 'Password change successful.<br><br>';
            }
            else {
                return 'Error changing password.<br><br>';
            }
        }
        else {
            return 'Error 0x1c2u3r';
        }
    }

    /**
     * Delete user
     *
     * @return string - Success message
     */
    public function deleteUser() {
        $uid = $this->userInfo['userid'];
        
        // To ensure at least one admin account is available,
        // some checks are performed to verify rights of accounts
        if (!empty($uid) && $uid != $_SESSION['userInfo']['userid']) {
            $delete = false;
            
            $stmt = 'SELECT `role` FROM `' . DB_PREFIX . 'users` WHERE `userid` = :userid';
            $params = array(
                'userid' => $uid 
            );
            $user = $this->queryDB($stmt, $params)[0]['role'];
            
            $perms = new Permissions();
            $isAdmin = (array) $perms->loadRights($user);
            
            if (!$isAdmin['admin']) {
                // If the account being deleted isn't an admin, then there's nothing to worry about
                $delete = true;
            }
            else {
                // If the account IS an admin, check all other users to make sure
                // there's at least one other user with the admin rights flag
                $stmt = 'SELECT `role` FROM `' . DB_PREFIX . 'users` WHERE `userid` != :userid';
                $params = array(
                    'userid' => $uid 
                );
                $otherUsers = $this->queryDB($stmt, $params);
                
                foreach ($otherUsers as $areTheyAdmin) {
                    $isAdmin = (array) $perms->loadRights($areTheyAdmin['role']);
                    
                    if ($isAdmin['admin']) {
                        // If one is found, stop for loop and allow the delete
                        $delete = true;
                        break;
                    }
                }
            }
            
            if ($delete) {
                $sql = 'DELETE u, p, a, m
                        FROM ' . DB_PREFIX . 'users AS u
                        LEFT JOIN ' . DB_PREFIX . 'presence AS p
                            ON p.uid = u.userid
                        LEFT JOIN ' . DB_PREFIX . 'apikeys AS a
                            ON a.user = u.userid
                        LEFT JOIN ' . DB_PREFIX . 'mail AS m
                            ON m.toUser = u.userid
                        WHERE u.userid = :userid';
                $params = array(
                    'userid' => $uid 
                );
                
                if ($this->queryDB($sql, $params)) {
                    return 'Action Taken: User Deleted<br><br>';
                }
                else {
                    return 'There was an error deleteing the user<br><br>';
                }
            }
            else {
                return '<br>There must be at least one account with the \'admin\' rights flag.<br>';
            }
        }
        else {
            return 'Error 0x1c2u3d';
        }
    }

    /**
     * Update user status
     *
     * @return string - Success message
     */
    public function updateUserStatus() {
        $uid = $this->userInfo['userid'];
        $status_id = $this->userCheesto['status'];
        $message = $this->userCheesto['message'];
        $returntime = $this->userCheesto['returntime'];
        
        if (!empty($uid) && !empty($status_id)) {
            $date = new \DateTime();
            $date = $date->format('Y-m-d H:i:s');
            
            switch ($status_id) {
                case "Available":
                    $status_id = 1;
                    $returntime = '00:00:00';
                    $message = '';
                    break;
                case "Away From Desk":
                    $status_id = 2;
                    break;
                case "At Lunch":
                    $status_id = 3;
                    break;
                case "Out for Day":
                    $status_id = 4;
                    break;
                case "Out":
                    $status_id = 5;
                    break;
                case "Appointment":
                    $status_id = 6;
                    break;
                case "Do Not Disturb":
                    $status_id = 7;
                    break;
                case "Meeting":
                    $status_id = 8;
                    break;
                case "Out Sick":
                    $status_id = 9;
                    break;
                case "Vacation":
                    $status_id = 10;
                    break;
                default:
                    $status_id = 1;
                    $returntime = "00:00:00";
                    $message = '';
                    break;
            }
            
            $stmt = 'UPDATE `' . DB_PREFIX . 'presence` SET `message` = :message, `status` = :status, `returntime` = :return, `dmodified` = :date WHERE `uid` = :userid';
            $params = array(
                'message' => $message,
                'status' => $status_id,
                'return' => $returntime,
                'date' => $date,
                'userid' => $uid 
            );
            
            if ($this->queryDB($stmt, $params)) {
                return 'User Status Updated<br><br>';
            }
            else {
                return 'Error saving user status.<br><br>';
            }
        }
        else {
            return 'Error 0x1c2u3c';
        }
    }

    /**
     * Revoke API keys for user
     *
     * @return string - Message
     */
    public function revokeAPIKey() {
        $sql = 'DELETE FROM ' . DB_PREFIX . 'apikeys
                WHERE user = :id';
        $params = array(
            "id" => $this->userInfo['userid'] 
        );
        
        if ($this->queryDB($sql, $params)) {
            $this->userApi = array();
            return 'API Key has been revoked<br><br>';
        }
        else {
            return 'Error 0x1c2u3r';
        }
    }
}