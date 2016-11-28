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

use Dandelion\Utils\Configuration as Config;

use Onesimus\Session\SessionManager as SM;

/**
 * The implementation of this class is a temporary solution.
 * Right now it's simply a wrapper for the Onesimus session manager.
 */
class SessionManager
{
    private function __construct() {}
    private function __clone() {}

    public static function register()
    {
        $options = [
            'timeout' => Config::get('sessionTimeout') * 60,
            'gclotto' => Config::get('gcLottery'),
            'table' => 'dan_session'
        ];

        // Simplist way to get the PDO object for the database
        $repoName = "\\Dandelion\\Repos\\SessionRepo";
        $repo = new $repoName();
        $pdo = $repo->getPDO();

        SM::register($pdo, $options);
        return;
    }

    public static function startSession($name)
    {
        SM::startSession($name);
    }

    /**
     * Return session data named $name. If it doesn't exist, return $else.
     * @param  mixed $name Name of session data to return
     * @param  mixed $else Value to return if session doesn't contine data $name
     * @return mixed
     */
    public static function get($name, $else = null)
    {
        return SM::get($name, $else);
    }

    /**
     * Set/overwrite session data named $name with data $value.
     * @param mixed $name  Name of session data to set
     * @param mixed $value Value of $name
     */
    public static function set($name, $value)
    {
        SM::set($name, $value);
    }

    /**
     * Merge arrays in session data
     * @param  mixed $name   Name of session data to merge
     * @param  array  $value Array to merge
     */
    public static function merge($name, array $value)
    {
        SM::merge($name, $value);
    }

    /**
     * Remove data from the session
     * @param  string $name Name of data to remove
     */
    public static function remove($name)
    {
        SM::remove($name);
    }

    /**
     * Clear session data
     */
    public static function clear()
    {
        SM::clear();
    }
}
