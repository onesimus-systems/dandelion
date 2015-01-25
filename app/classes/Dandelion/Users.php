<?php
/**
 * User management
 */
namespace Dandelion;

use \Dandelion\Permissions;
use \Dandelion\Storage\Contracts\DatabaseConn;

class Users
{
    // User data
    public $userInfo = array();
    public $userCheesto = array();
    public $userApi = array();

    public function __construct(DatabaseConn $db, $uid = -1, $load = false) {
        $this->db = $db;

        if ($uid >= 0) {
            $this->userInfo['userid'] = $uid;
        } else {
            $this->userInfo['userid'] = null;
        }

        if ($load === true && $uid >= 0) {
            $this->loadUser();
        } elseif ($load === true && $uid < 0) {
            trigger_error('To load a user you must provide a user ID.');
        }
        return true;
    }

    public function loadUser() {
        $this->db->select('u.userid AS u_userid,
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
                        r.permissions AS u_permissions')
                 ->from(DB_PREFIX . 'users AS u
                    LEFT JOIN ' . DB_PREFIX . 'presence AS p
                        ON p.uid = u.userid
                    LEFT JOIN ' . DB_PREFIX . 'apikeys AS a
                        ON a.user = u.userid
                    LEFT JOIN ' . DB_PREFIX . 'rights AS r
                        ON r.role = u.role')
                 ->where('u.userid = :userid');

        $params = array(
            'userid' => $this->userInfo['userid']
        );

        $allUserInfo = $this->db->get($params);

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

    public function saveUser() {
        if (empty($this->userInfo['realname'])
            || empty($this->userInfo['role'])
            || (empty($this->userInfo['firsttime']) && $this->userInfo['firsttime'] != 0)
            || empty($this->userInfo['userid'])) {
            return 'Something is empty';
        }

        $this->userInfo['role'] = strtolower($this->userInfo['role']);
        // Update main user row
        $this->db->update(DB_PREFIX.'users')
                 ->set('realname = :realname, role = :role, firsttime = :first, theme = :theme')
                 ->where('userid = :userid');
        $params = array(
            'realname' => $this->userInfo['realname'],
            'role' => $this->userInfo['role'],
            'first' => $this->userInfo['firsttime'],
            'userid' => $this->userInfo['userid'],
            'theme' => $this->userInfo['theme']
        );
        $this->db->go($params);

        // Update Cheesto information
        $this->db->update(DB_PREFIX.'presence')
                 ->set('realname = :realname')
                 ->where('uid = :userid');
        $params = array(
            'realname' => $this->userInfo['realname'],
            'userid' => $this->userInfo['userid']
        );

        if (!$this->db->go($params)) {
            return 'There was an error saving the user';
        }

        return true;
    }

    public function createUser($username, $password, $realname, $role, $cheesto = true) {
        $date = new \DateTime();

        // Error checking
        if (empty($username) || empty($password) || empty($realname) || empty($role)) {
            return 'Something is empty';
        }
        if ($this->isUser($username)) {
            return 'Username already in use';
        }

        $role = strtolower($role);
        // Create row in users table
        $this->db->insert()
                 ->into(DB_PREFIX.'users', array('username', 'password', 'realname', 'role', 'datecreated', 'theme'))
                 ->values(array(':username', ':password', ':realname', ':role', ':datecreated', '\'\''));
        $params = array(
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'realname' => $realname,
            'role' => $role,
            'datecreated' => $date->format('Y-m-d')
        );
        $this->db->go($params);

        if ($cheesto) {
            $lastID = $this->db->lastInsertId();

            // Create row in presence table
            $this->db->insert()
                     ->into(DB_PREFIX.'presence', array('uid', 'realname', 'status', 'message', 'returntime', 'dmodified'))
                     ->values(array(':uid', ':real', 0, '\'\'', '\'00:00:00\'', ':date'));
            $params = array(
                'uid' => $lastID,
                'real' => $realname,
                'date' => $date->format('Y-m-d H:i:s')
            );

            $this->db->go($params);
        }

        return true;
    }

    private function isUser($username) {
        $this->db->select()
                 ->from(DB_PREFIX.'users')
                 ->where('username = :username');
        $params = array(
            'username' => $username
        );
        $row = $this->db->get($params);
        return !empty($row);
    }

    public function resetPassword($pass = '') {
        $uid = $this->userInfo['userid'];

        if (empty($uid) || empty($pass)) {
            return 'Something is empty';
        }

        $pass = password_hash($pass, PASSWORD_BCRYPT);

        $this->db->update(DB_PREFIX.'users')->set('password = :newpass, firsttime = 0')->where('userid = :id');
        $params = array(
            'newpass' => $pass,
            'id' => $uid
        );

        if ($this->db->go($params)) {
            return true;
        }
        else {
            return 'Error changing password.';
        }
    }

    public function deleteUser($uid) {
        if (empty($uid)) {
            if (!empty($this->userInfo['userid'])) {
                $uid = $this->userInfo['userid'];
            } else {
                return 'No user id provided';
            }
        }

        $delete = false;

        $this->db->select('role')
                 ->from(DB_PREFIX.'users')
                 ->where('userid = :userid');
        $params = array(
            'userid' => $uid
        );
        $userRole = $this->db->get($params)[0]['role'];

        $perms = new Permissions($this->db);
        $isAdmin = (array) $perms->loadRights($userRole);

        if (!$isAdmin['admin']) {
            // If the account being deleted isn't an admin, then there's nothing to worry about
            $delete = true;
        } else {
            // If the account IS an admin, check all other users to make sure
            // there's at least one other user with the admin rights flag
            $this->db->select('role')
                     ->from(DB_PREFIX.'users')
                     ->where('userid != :userid');
            $params = array(
                'userid' => $uid
            );
            $otherUsers = $this->db->get($params);

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
            $this->db->delete('u, p, a, m')
                     ->from(DB_PREFIX . 'users AS u LEFT JOIN ' . DB_PREFIX . 'presence AS p ON p.uid = u.userid LEFT JOIN ' . DB_PREFIX . 'apikeys AS a ON a.user = u.userid LEFT JOIN ' . DB_PREFIX . 'mail AS m ON m.toUser = u.userid')
                     ->where('u.userid = :userid');
            $params = array(
                'userid' => $uid
            );

            if ($this->db->go($params)) {
                return true;
            } else {
                return 'Error deleting user';
            }
        } else {
            return 'At least one admin account must be left to delete another admin account';
        }
    }

    public function getUserList() {
        $this->db->select('userid, realname, username, role, datecreated, theme, firsttime')
                 ->from(DB_PREFIX.'users');
        return $this->db->get();
    }

    public function getUser($uid) {
        $this->db->select('userid, realname, username, role, datecreated, theme, firsttime')
                 ->from(DB_PREFIX.'users')
                 ->where('userid = :uid');
        $params = array('uid' => $uid);
        return $this->db->get($params)[0];
    }
}
