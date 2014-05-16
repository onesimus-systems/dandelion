<?php
/**
 * Class autoloader for Dandelion
 *
 * @author Lee Keitel
 * @date May, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

function dandy_autoload($className)
{
	$classInfo = array_reverse(explode('\\', $className));
    $className = strtolower($classInfo[0]);

    if (file_exists(ROOT . "/classes/{$className}.php")) {
        require_once(ROOT . "/classes/{$className}.php");
    } else {
        trigger_error("Class '{$className}' was not able to load.", E_USER_ERROR);
    }
}

spl_autoload_register('Dandelion\dandy_autoload');
