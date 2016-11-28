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
use Dandelion\User;
use Dandelion\Factories\UserFactory;

class GateKeeper
{
    // Array of task => permissions needed
    private static $taskPermissions = [
        // For arrays, the first element must be one of these:
        // 'r1' = requires at lease one permission in the set
        // 'ra' = requires all permissions in the set
        // Defaults to 'ra'
        'manage_current_users' => ['r1', 'edituser', 'deleteuser'],
        'manage_users' => ['r1', 'createuser', 'edituser', 'deleteuser'],

        'manage_current_groups' => ['r1', 'editgroup', 'deletegroup'],
        'manage_groups' => ['r1', 'creategroup', 'editgroup', 'deletegroup'],

        'manage_current_categories' => ['r1', 'editcat', 'deletecat'],
        'manage_categories' => ['r1', 'createcat', 'editcat', 'deletecat'],

        'create_log' => 'createlog',
        'edit_log' => 'editlog',
        'edit_any_log' => 'editlogall',
        'view_log' => 'viewlog',
        'add_comment' => 'addcomment',

        'create_cat' => 'createcat',
        'edit_cat' => 'editcat',
        'delete_cat' => 'deletecat',

        'create_user' => 'createuser',
        'edit_user' => 'edituser',
        'delete_user' => 'deleteuser',

        'create_group' => 'creategroup',
        'edit_group' => 'editgroup',
        'delete_group' => 'deletegroup',

        'view_cheesto' => 'viewcheesto',
        'update_cheesto' => 'updatecheesto',

        'admin' => 'admin'
    ];

    public function __construct()
    {
    }

    /**
     * Perform a user logon.
     */
    public function login($username, $password, $remember = false)
    {
        if (!$username || !$password) {
            return false;
        }

        $user = $this->checkUser($username, $password);

        if (!$user) {
            return false;
        }

        session_regenerate_id();

        // Set primary session data
        $userData = $user->get([
            'id',
            'username',
            'fullname',
            'group_id',
            'created',
            'initial_login',
            'logs_per_page',
            'theme',
            'disabled'
        ]);
        Session::set('loggedin', true);
        Session::set('userInfo', $userData);

        if ($remember) {
            // Set remember me cookie
            setcookie('dan_username', $userData['username'], time() + 60 * 60 * 24 * 30, '/');
        }

        return $userData['initial_login']+1;
    }

    /**
     * Checks if a provided username is an actual user
     * and if the provided password is correct.
     *
     * @param string $username
     * @param string $password
     *
     * @return User object or null
     */
    private function checkUser($username, $password)
    {
        $uf = new UserFactory();
        $user = $uf->getByUsername($username);

        if ($user->isValid() && $user->enabled()) {
            $pass = $user->get('password');
            if (password_verify($password, $pass)) {
                return $user;
            }
        }

        return null;
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

    public static function authorized(User $user, $task)
    {
        $keycard = $user->getKeycard();

        if (!array_key_exists($task, self::$taskPermissions)) {
            return false;
        }

        $neededPermissions = self::$taskPermissions[$task];
        if (!is_array($neededPermissions)) {
            // Simple mapping of task to permission
            return $keycard->read($neededPermissions);
        }

        // Multiple permissions need checked
        $mode = $neededPermissions[0];
        if ($mode != 'r1' && $mode != 'ra') {
            // Mode defaults to require all
            $mode = 'ra';
        } else {
            // Remove mode from front of array
            array_shift($neededPermissions);
        }

        foreach ($neededPermissions as $permission) {
            if ($mode === 'r1' && $keycard->read($permission) === true) {
                return true;
            }

            if ($mode === 'ra' && $keycard->read($permission) === false) {
                return false;
            }
        }

        switch ($mode) {
        case 'r1':
            return false;
        case 'ra':
            return true;
        }
    }
}
