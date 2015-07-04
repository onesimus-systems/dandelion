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

    public static function get($name, $else = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $else;
    }

    public static function set($name, $value)
    {
        if (is_array($value) && self::get($name)) {
            $_SESSION[$name] = array_merge($_SESSION[$name], $value);
        } else {
            $_SESSION[$name] = $value;
        }
    }

    public static function clear()
    {
        $_SESSION = [];
    }
}
