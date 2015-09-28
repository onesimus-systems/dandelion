<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Session;

use Dandelion\Application;
use Dandelion\Utils\Configuration as Config;

class SessionManager
{
    private static $handler;

    private function __construct() {}
    private function __clone() {}

    public static function register(Application $app)
    {
        if (self::$handler !== null) {
            return;
        }

        $timeout = Config::get('sessionTimeout') * 60;
        $gcLotto = Config::get('gcLottery');

        self::$handler = new SessionHandler($app, $timeout, $gcLotto);

        session_set_save_handler(self::$handler, true);
        return;
    }

    public static function startSession($name)
    {
        session_name($name);
        session_start();
        return;
    }

    /**
     * Return session data named $name. If it doesn't exist, return $else.
     * @param  mixed $name Name of session data to return
     * @param  mixed $else Value to return if session doesn't contine data $name
     * @return mixed
     */
    public static function get($name, $else = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $else;
    }

    /**
     * Set/overwrite session data named $name with data $value.
     * @param mixed $name  Name of session data to set
     * @param mixed $value Value of $name
     */
    public static function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Merge arrays in session data
     * @param  mixed $name   Name of session data to merge
     * @param  array  $value Array to merge
     */
    public static function merge($name, array $value) {
        $_SESSION[$name] = array_merge($_SESSION[$name], $value);
    }

    /**
     * Remove data from the session
     * @param  string $name Name of data to remove
     */
    public static function remove($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Clear session data
     */
    public static function clear()
    {
        $_SESSION = [];
    }
}
