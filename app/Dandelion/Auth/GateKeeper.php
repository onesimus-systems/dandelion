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

class Permission
{
    const SINGLE = 'single';
    const REQ1 = 'req1';
    const REQALL = 'reqall';

    private $mode;
    private $permissions;

    public function __construct($mode, Array $permissions)
    {
        $this->mode = $mode;
        $this->permissions = $permissions;
    }

    public function granted($keycard)
    {
        switch ($this->mode) {
        case self::SINGLE:
            return $keycard->read($this->permissions[0]);
        case self::REQ1:
            return $this->grantedR1($keycard);
        case self::REQALL:
            return $this->grantedRA($keycard);
        }
    }

    private function grantedR1($keycard)
    {
        foreach ($this->permissions as $permission) {
            if ($keycard->read($permission) === true) {
                return true;
            }
        }
        return false;
    }

    private function grantedRA($keycard)
    {
        foreach ($this->permissions as $permission) {
            if ($keycard->read($permission) === false) {
                return false;
            }
        }
        return true;
    }
}

class GateKeeper
{
    private static $instance;
    private $taskPermissions = [];

    private function __construct()
    {
        $this->taskPermissions = [
            'manage_current_users' => new Permission(Permission::REQ1, ['edituser', 'deleteuser']),
            'manage_users' => new Permission(Permission::REQ1, ['createuser', 'edituser', 'deleteuser']),

            'manage_current_groups' => new Permission(Permission::REQ1, ['editgroup', 'deletegroup']),
            'manage_groups' => new Permission(Permission::REQ1, ['creategroup', 'editgroup', 'deletegroup']),

            'manage_current_categories' => new Permission(Permission::REQ1, ['editcat', 'deletecat']),
            'manage_categories' => new Permission(Permission::REQ1, ['createcat', 'editcat', 'deletecat']),

            'create_log' => new Permission(Permission::SINGLE, ['createlog']),
            'edit_log' => new Permission(Permission::SINGLE, ['editlog']),
            'edit_any_log' => new Permission(Permission::SINGLE, ['editlogall']),
            'view_log' => new Permission(Permission::SINGLE, ['viewlog']),
            'add_comment' => new Permission(Permission::SINGLE, ['addcomment']),

            'create_cat' => new Permission(Permission::SINGLE, ['createcat']),
            'edit_cat' => new Permission(Permission::SINGLE, ['editcat']),
            'delete_cat' => new Permission(Permission::SINGLE, ['deletecat']),

            'create_user' => new Permission(Permission::SINGLE, ['createuser']),
            'edit_user' => new Permission(Permission::SINGLE, ['edituser']),
            'delete_user' => new Permission(Permission::SINGLE, ['deleteuser']),

            'create_group' => new Permission(Permission::SINGLE, ['creategroup']),
            'edit_group' => new Permission(Permission::SINGLE, ['editgroup']),
            'delete_group' => new Permission(Permission::SINGLE, ['deletegroup']),

            'view_cheesto' => new Permission(Permission::SINGLE, ['viewcheesto']),
            'update_cheesto' => new Permission(Permission::SINGLE, ['updatecheesto']),

            'admin' => new Permission(Permission::SINGLE, ['admin']),
        ];
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new GateKeeper();
        }

        return self::$instance;
    }

    /**
     * Perform a user logon.
     */
    public static function login($username, $password, $remember = false)
    {
        if (!$username || !$password) {
            return false;
        }

        $user = self::checkUser($username, $password);

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
    private static function checkUser($username, $password)
    {
        $user = (new UserFactory())->getByUsername($username);

        if ($user->isValid() && $user->enabled()) {
            $pass = $user->get('password');
            if (password_verify($password, $pass)) {
                return $user;
            }
        }
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

    public function authorized(Keycard $keycard, $task)
    {
        if (!array_key_exists($task, $this->taskPermissions)) {
            return false;
        }

        return $this->taskPermissions[$task]->granted($keycard);
    }
}
