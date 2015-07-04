<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Auth;

use Dandelion\Session\SessionManager as Session;
use Dandelion\Repos\Interfaces\AuthRepo;

class GateKeeper
{
    public function __construct(AuthRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Perform a user logon.
     */
    public function login($user, $pass, $remember = false)
    {
        if (!$user || !$pass) {
            return false;
        }

        $userInfo = $this->isUser($user, $pass);

        if (!$userInfo) {
            return false;
        }

        session_regenerate_id();

        // Set primary session data
        unset($userInfo['password']);
        Session::set('loggedin', true);
        Session::set('userInfo', $userInfo);

        if ($remember) {
            // Set remember me cookie
            setcookie('dan_username', $userInfo['username'], time() + 60 * 60 * 24 * 30, '/');
        }

        return $userInfo['initial_login']+1;
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
        $user = $this->repo->isUser($user);

        if ($user) {
            if ($user['password'] && password_verify($pass, $user['password'])) { // Check if password is correct
                return $user;
            }
        }

        return false;
    }

    /**
     * Check if a user is authenticated
     */
    public static function authenticated()
    {
        return Session::get('loggedin', false);
    }

    public static function logout()
    {
        Session::clear();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
            );
        }
        session_unset();
        session_destroy();
    }
}
