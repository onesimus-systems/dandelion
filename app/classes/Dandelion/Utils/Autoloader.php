<?php
/**
 * Autoloader for Dandelion
 */
namespace Dandelion\Utils;

class Autoloader
{
    public static function dandy_autoload($className)
    {
        $classInfo = explode('\\', $className);
        $className = array_pop($classInfo);
        $namespace = implode('/', $classInfo);

        // Load case-sensative ClassName per PSR-4
        if (file_exists(ROOT."/classes/{$namespace}/{$className}.php")) {
            require (ROOT."/classes/{$namespace}/{$className}.php");
            return;
        } elseif (file_exists(ROOT."/controllers/{$className}.php")) {
            require (ROOT."/controllers/{$className}.php");
            return;
        } elseif (file_exists(ROOT . "/classes/{$className}.php")) {
            require (ROOT . "/classes/{$className}.php");
            return;
        }

        // Load API modules
        $className = strtolower($className);
        if (file_exists(ROOT."/classes/{$namespace}/{$className}.php")) {
            require (ROOT."/classes/{$namespace}/{$className}.php");
        } else {
            trigger_error("Class '{$namespace}/{$className}' was not able to load.", E_USER_ERROR);
        }
    }

    public static function register() {

    }
}
