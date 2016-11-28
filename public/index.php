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

/**
 * Register Composer's autoloader
 */
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo 'Error: Composer doesn\'t appear to have been installed. Please install <a href="https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx" target="_blank">Composer</a> and then run either: <br><br>Locally: <pre>$ php composer.phar install --no-dev</pre> <br>Globally: <pre>$ composer install --no-dev</pre>';
    exit(1);
}
require __DIR__.'/../vendor/autoload.php';

/**
 * Bootstrap.php does quite a bit of set for Dandelion
 */
$app = require __DIR__.'/../bootstrap/start.php';

/**
 * And finally, Dandelion! Let's run this thing!
 */
$app->run();
