<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Utils;

class Configuration
{
    private static $loaded = false;
    private static $config = [];

    private static $basePath = '';

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function load($configPath)
    {
        self::$basePath = $configPath;
        $defaultSettingsFile = $configPath.'/config.defaults.php';
        $userSettingsFile = $configPath.'/config.php';

        if (!file_exists($defaultSettingsFile) || !file_exists($userSettingsFile)) {
            return false;
        }

        if (!self::$loaded) {
            // Load defaults, the default file has all possible config options
            $defaults = include $defaultSettingsFile;
            // Load user specified values
            $userSettings = include $userSettingsFile;

            if (!is_array($userSettings)) {
                return false;
            }

            // Merge the settings
            foreach ($defaults as $key => $value) {
                if (isset($userSettings[$key])) {
                    $defaults[$key] = $userSettings[$key];
                }
            }

            self::$config = $defaults;
            self::$config['hostname'] = rtrim(self::$config['hostname'], '/');
            self::$loaded = true;
        }
        return self::$config;
    }

    public static function getConfig()
    {
        return self::$config;
    }

    public static function set($name, $value)
    {
        self::$config[$name] = $value;
    }

    public static function get($name, $else = null)
    {
        return isset(self::$config[$name]) ? self::$config[$name] : $else;
    }
}
