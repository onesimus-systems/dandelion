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
    $classInfo = array_reverse(explode('\\', $className));
    $className = strtolower($classInfo[0]);
    $namespace = $classInfo[1];

    // All API files have an uppercase API at the end of their filenames
    $isApi = substr($className, -3);
    if ($isApi == 'api') {
        $className = str_replace('api', 'API', $className);
    }

    // Load normal class
    if (file_exists(ROOT . "/classes/{$className}.php")) {
        require (ROOT . "/classes/{$className}.php");
    }
    // Load API class
    elseif (file_exists(ROOT."/api/{$className}.php")) {
        require (ROOT."/api/{$className}.php");
    }
    elseif (file_exists(ROOT."/classes/{$namespace}/{$className}.php")) {
        require (ROOT."/classes/{$namespace}/{$className}.php");
    }
    // Error if class not found
    else {
        trigger_error("Class '{$className}' was not able to load.", E_USER_ERROR);
    }
}

spl_autoload_register('Dandelion\dandy_autoload');
