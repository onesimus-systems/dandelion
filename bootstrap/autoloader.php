<?php
/**
 * Class autoloader for Dandelion
 *
 * @author Lee Keitel
 *         @date May, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 *
 */
namespace Dandelion;

function dandy_autoload($className)
{
    $classInfo = explode('\\', $className);
    $className = array_pop($classInfo);
    $namespace = implode('/', $classInfo);
    $rootDir = __DIR__.'/../app';

    // Load API modules
    $className = strtolower($className);
    if (file_exists($rootDir."/classes/{$namespace}/{$className}.php")) {
        require ($rootDir."/classes/{$namespace}/{$className}.php");
    } else {
        trigger_error("Class '{$namespace}/{$className}' was not able to load.", E_USER_ERROR);
    }
}

spl_autoload_register('Dandelion\dandy_autoload');
