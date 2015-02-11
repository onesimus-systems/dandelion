<?php
/**
 * Configuration manager for Dandelion
 */
namespace Dandelion\Utils;

class Configuration
{
    private static $loaded = false;
    private static $config;

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function load($paths)
    {
        if (!file_exists($paths['app'] . '/config/config.php')) {
            return false;
        }

        if (!self::$loaded) {
            self::$config = include $paths['app'] . '/config/config.php';
            self::$loaded = true;
        }
        return self::$config;
    }

    public static function getConfig()
    {
        if (!self::$loaded) {
            return null;
        }
        return self::$config;
    }
}
