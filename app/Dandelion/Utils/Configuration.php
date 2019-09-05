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

    public static function load($configPath)
    {
        if (is_array($configPath)) {
            self::$config = $configPath;
        } elseif (!self::$loaded) {
            $defaultSettingsFile = $configPath.'/config.defaults.php';
            $userSettingsFile = $configPath.'/config.php';

            if (!file_exists($defaultSettingsFile) || !file_exists($userSettingsFile)) {
                return false;
            }

            // Load defaults, the default file has all possible config options
            $defaults = include $defaultSettingsFile;
            // Load user specified values
            $config = [];
            $legacyConfig = include $userSettingsFile;
            // Support the older style of returning an array
            if ($legacyConfig !== 1) {
                $config = $legacyConfig;
            }

            if (!is_array($config)) {
                return false;
            }

            self::$config = self::arrayMergeRecursiveDeep($defaults, $config);
            // Ensure hostname is formatted properly for rest of application
            self::$config['hostname'] = rtrim(self::$config['hostname'], '/');
        }

        self::$loaded = true;
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

    /**
     * Recursively merges arrays but doesn't append values like PHP's array_merge_recursive()
     *
     * @param array $ Arrays to merge, values in later arrays will overwrite earlier values
     * @return array Merged values
     *
     * @SuppressWarnings(PHPMD.ElseExpressions)
     */
    private static function arrayMergeRecursiveDeep()
    {
        $arrays = func_get_args();
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_integer($key)) {
                    $result[] = $value;
                } elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = self::arrayMergeRecursiveDeep($result[$key], $value);
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}
