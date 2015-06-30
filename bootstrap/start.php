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

use \Dandelion\Application;

$paths = require __DIR__.'/paths.php';

// Check PHP version, Dandelion supports only PHP 5.4 and above
if (!function_exists('version_compare') || version_compare(PHP_VERSION, '5.4.0', '<')) {
    exit(1);
}

$app = new Application();

$app->bindInstallPaths($paths, true);

return $app;
