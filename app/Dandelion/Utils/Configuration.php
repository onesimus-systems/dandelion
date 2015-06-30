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

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function load($configPath)
    {
        if (!file_exists($configPath)) {
            return false;
        }

        if (!self::$loaded) {
            self::$config = include $configPath;
            self::$config['hostname'] = rtrim(self::$config['hostname'], '/');
            self::$loaded = true;
        }
        return self::$config;
    }

    public static function getConfig()
    {
        return self::$config;
    }
}
