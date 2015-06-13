<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
  */
namespace Dandelion;

function dandelionAutoloader($className)
{
    $classInfo = explode('\\', $className);
    $className = array_pop($classInfo);
    $namespace = implode('/', $classInfo);
    $rootDir = __DIR__.'/../app';

    // Load API modules
    $className = strtolower($className);
    if (file_exists($rootDir."/{$namespace}/{$className}.php")) {
        require ($rootDir."/{$namespace}/{$className}.php");
    } else {
        trigger_error("Class '{$namespace}/{$className}' was not able to load.", E_USER_ERROR);
    }
}

spl_autoload_register('Dandelion\dandelionAutoloader');
