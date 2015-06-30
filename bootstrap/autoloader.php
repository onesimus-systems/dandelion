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

function dandelionApiAutoloader($className)
{
    $rootDir = __DIR__.'/../app/Dandelion/API/Module/';
    $classMap = [
        'dandelion\api\module\adminapi'        => 'adminapi.php',
        'dandelion\api\module\categoriesapi'   => 'categoriesapi.php',
        'dandelion\api\module\cheestoapi'      => 'cheestoapi.php',
        'dandelion\api\module\groupsapi'       => 'groupsapi.php',
        'dandelion\api\module\keymanagerapi'   => 'keymanagerapi.php',
        'dandelion\api\module\logsapi'         => 'logsapi.php',
        'dandelion\api\module\usersapi'        => 'usersapi.php',
        'dandelion\api\module\usersettingsapi' => 'usersettingsapi.php'
    ];

    $className = strtolower($className);
    if (array_key_exists($className, $classMap)) {
        include $rootDir.$classMap[$className];
    } else {
        trigger_error("Class '{$className}' was not found.", E_USER_ERROR);
    }
}

spl_autoload_register('Dandelion\dandelionApiAutoloader');
