<?php
/**
 *
 *
 */

namespace Dandelion\Gatekeeper;

class authenticate {
    public function __construct(\Dandelion\databaseConn $db) {
        $this->db = $db;
    }

    /**
     * Perform a user logon.
     */
    public function login($user, $pass, $remember = false) {
        $userInfo = $this->isUser($user, $pass);

        if (!$userInfo) {
            trigger_error('Failed login attempt for '.$username.' at ' . date("Y-m-d H:i:s"), E_USER_WARNING);
            return 'Incorrect username or password';
        }

        if (ini_get("session.use_cookies")) {
            // Set session cookie
            setcookie(session_name(), $_COOKIE[session_name()], time()+60*60*22, '/');
        }

        // Set primary session data
        $_SESSION['loggedin'] = true;
        $_SESSION['userInfo'] = $userInfo;

        if ($remember) {
            // Set remember me cookie
            setcookie('dan_username', $_SESSION['userInfo']['username'], time()+60*60*24*30, '/');
        }

        switch($userInfo['firsttime']) {
            case 2:
                return '1';
                break;
            default:
                return '0';
                break;
        }

        return 'Error authenticating user';
    }

    /**
    * Checks if a provided username is an actual user
    * and if the provided password is correct.
    *
    * @param string $user - Username
    * @param string $pass - Password
    *
    * @return bool or array - Array containing row of user data from database, false on error
    */
    private function isUser($user, $pass) {
        $this->db->select()
                 ->from(DB_PREFIX.'users')
                 ->where('username = :user');
        $param = array('user' => $user);
        $user = $this->db->get($param);

        if (!empty($user[0]['password']) && password_verify($pass, $user[0]['password'])) { // Check if password is correct
            return $user[0];
        }

        return false;
    }
}
