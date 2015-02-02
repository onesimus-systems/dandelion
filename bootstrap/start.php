<?php
/**
 * This file is a global file which is included on every page.
 * This script is used to define any global aspects of Dandelion
 * and include other needed PHP scripts.
 *
 * @author Lee Keitel
 *         @date March 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 *
 */
namespace Dandelion;

use \Dandelion\Application;
use \Dandelion\Storage\MySqlDatabase;

// Check PHP version, Dandelion supports only PHP 5.4 and above
if (!function_exists('version_compare') || version_compare(PHP_VERSION, '5.4.0', '<')) {
    exit(1);
}

// Load password comapatability library if version is less than 5.5
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require ROOT . '/lib/password-compat/password.php';
}

$app = new Application();

$app->bindInstallPaths(require __DIR__.'/paths.php');

return $app;
