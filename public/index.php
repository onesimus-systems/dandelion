<?php
/**
  * Dandelion Logbook - Keeping track of events so you remember what you did last week.
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @package Dandelion
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

use \Dandelion\Application;

/**
 * Register Composer's autoloader
 */
require __DIR__.'/../vendor/autoload.php';

/**
 * Register the Dandelion specific autoloader for the API
 */
require __DIR__.'/../bootstrap/autoloader.php';

/**
 * Bootstrap.php does quite a bit of set for Dandelion
 */
$app = require __DIR__.'/../bootstrap/start.php';

/**
 * And finally, Dandelion! Let's run this thing!
 */
$app->run();
