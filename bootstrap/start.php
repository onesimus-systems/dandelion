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

use Onesimus\Router\Http\Request;

// Check PHP version, Dandelion supports only PHP 7.0 and above
if (!function_exists('version_compare') || version_compare(PHP_VERSION, '7.0.0', '<')) {
    exit(1);
}

$paths = require __DIR__.'/paths.php';

$app = Application::getInstance(Request::getRequest());
$app->bindInstallPaths($paths, true);

return $app;
