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

require_once 'lib/bootstrap.php';

// Instantiate and run application
$app = new DandelionApplication($_SERVER['REQUEST_URI']);
$app->run();
