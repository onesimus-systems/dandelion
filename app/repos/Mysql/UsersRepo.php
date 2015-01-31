<?php
/**
 * MySQL repository for administration module
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class UsersRepo extends BaseMySqlRepo implements Interfaces\UsersRepo
{
    public function getFullUser($uid)
    {
        $this->database->select('u.userid AS u_userid,
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
            ->from($this->prefix . 'users AS u
                    LEFT JOIN ' . $this->prefix . 'presence AS p
                        ON p.uid = u.userid
                    LEFT JOIN ' . $this->prefix . 'apikeys AS a
                        ON a.user = u.userid
                    LEFT JOIN ' . $this->prefix . 'rights AS r
                        ON r.role = u.role')
            ->where('u.userid = :uid');

        return $this->database->getFirst(['uid' => $uid]);
    }

    public function saveUser($uid, $realname, $role, $theme, $first)
    {
        $this->database->update($this->prefix . 'users')
            ->set(['realname = :realname', 'role = :role', 'firsttime = :first', 'theme = :theme'])
            ->where('userid = :userid');

        $params = array(
            'realname' => $realname,
            'role'     => $role,
            'first'    => $first,
            'userid'   => $uid,
            'theme'    => $theme
        );

        return $this->database->go($params);
    }

    public function saveUserCheesto($uid, $realname)
    {
        $this->database->update($this->prefix . 'presence')
            ->set('realname = :realname')
            ->where('uid = :uid');

        return $this->database->go(['realname' => $realname, 'uid' => $uid]);
    }

    public function createUser($username, $password, $realname, $role, $date)
    {

        $this->database->insert()
            ->into($this->prefix . 'users', ['username', 'password', 'realname', 'role', 'datecreated', 'theme'])
            ->values([':username', ':password', ':realname', ':role', ':datecreated', '\'\'']);

        $params = [
            'username'    => $username,
            'password'    => $password,
            'realname'    => $realname,
            'role'        => $role,
            'datecreated' => $date
        ];

        return $this->database->go($params);
    }

    public function lastCreatedUserId()
    {
        return $this->database->lastInsertId();
    }

    public function createUserCheesto($uid, $realname, $date)
    {
        $this->database->insert()
            ->into($this->prefix . 'presence',
                ['uid', 'realname', 'status', 'message', 'returntime', 'dmodified'])
            ->values([':uid', ':real', 0, '\'\'', '\'00:00:00\'', ':date']);

        $params = [
            'uid'  => $uid,
            'real' => $realname,
            'date' => $date
        ];

        return $this->database->go($params);
    }

    public function isUser($username)
    {
        $this->database->select()
            ->from($this->prefix . 'users')
            ->where('username = :username');

        return $this->database->getFirst(['username' => $username]);
    }

    public function resetPassword($uid, $pass)
    {
        $this->database->update($this->prefix . 'users')
            ->set(['password = :pass', 'firsttime = 0'])
            ->where('userid = :uid');

        return $this->database->go(['pass' => $pass, 'uid' => $uid]);
    }

    public function deleteUser($uid)
    {
        $this->database->delete('u, p, a, m')
            ->from($this->prefix . 'users AS u
                                LEFT JOIN ' . $this->prefix . 'presence AS p
                                    ON p.uid = u.userid
                                LEFT JOIN ' . $this->prefix . 'apikeys AS a
                                    ON a.user = u.userid
                                LEFT JOIN ' . $this->prefix . 'mail AS m
                                    ON m.toUser = u.userid')
            ->where('u.userid = :uid');

        return $this->database->go(['uid' => $uid]);
    }

    public function getUserRole($uid, $invert = false)
    {
        $this->database->select('role')
            ->from($this->prefix . 'users');

        if ($invert) {
            $this->database->where('userid != :uid');
            return $this->database->get(['uid' => $uid]);
        } else {
            $this->database->where('userid = :uid');
            return $this->database->getFirst(['uid' => $uid])['role'];
        }
    }

    public function getUserList()
    {
        $this->database->select('userid, realname, username, role, datecreated, theme, firsttime')
            ->from($this->prefix . 'users');
        return $this->database->get();
    }

    public function getUser($uid)
    {
        $this->database->select('userid, realname, username, role, datecreated, theme, firsttime')
            ->from($this->prefix . 'users')
            ->where('userid = :uid');

        return $this->database->getFirst(['uid' => $uid]);
    }
}
