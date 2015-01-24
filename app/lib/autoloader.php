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
    $className = strtolower(array_pop($classInfo));
    $namespace = implode('/', $classInfo);

    // Load normal namespaced class
    if (file_exists(ROOT."/classes/{$namespace}/{$className}.php")) {
        require (ROOT."/classes/{$namespace}/{$className}.php");
    }
    elseif (file_exists(ROOT . "/classes/{$className}.php")) {
        require (ROOT . "/classes/{$className}.php");
    }
    // Error if class not found
    else {
        trigger_error("Class '{$namespace}/{$className}' was not able to load.", E_USER_ERROR);
    }
}

spl_autoload_register('Dandelion\dandy_autoload');
