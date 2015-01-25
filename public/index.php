<?php
/**
  * This is the main entry point of Dandelion. All page and API requests are routed here.
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date Janurary 23
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

use \Dandelion\Controllers\AppController;

require __DIR__.'/../bootstrap/autoloader.php';

require __DIR__.'/../bootstrap/bootstrap.php';

$app = new AppController();
$app->run();
