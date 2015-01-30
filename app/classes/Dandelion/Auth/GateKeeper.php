<?php
/**
 * Authentication handler for Dandelion
 */
namespace Dandelion\Auth;

use \Dandelion\Storage\Contracts\DatabaseConn;

class GateKeeper
{
    public function __construct(DatabaseConn $db)
    {
        $this->db = $db;
    }

    /**
     * Perform a user logon.
     */
    public function login($user, $pass, $remember = false)
    {
        if (empty($user) || empty($pass)) {
            return false;
        }

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
                return '2';
                break;
            default:
                return '1';
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
    private function isUser($user, $pass)
    {
        $this->db->select()
                 ->from(DB_PREFIX.'users')
                 ->where('username = :user');
        $param = array('user' => $user);
        $user = $this->db->getFirst($param);

        if (!empty($user['password']) && password_verify($pass, $user['password'])) { // Check if password is correct
            return $user;
        }

        return false;
    }

    /**
     * Check if a user is authenticated
     */
    public static function authenticated()
    {
        $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;
        return $loggedin;
    }
}
