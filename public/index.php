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
 *  Let's register the autoloader that way we don't have to worry about includes
 */
require __DIR__.'/../bootstrap/autoloader.php';

/**
 *  Bootstrap.php does quite a bit of set for Dandelion
 */
$app = require __DIR__.'/../bootstrap/start.php';

/**
 *  And finally, Dandelion! Let's run this thing!
 */
$app->run();
