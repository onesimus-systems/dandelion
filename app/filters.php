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

use \Dandelion\Utils\View;
use \Dandelion\Auth\GateKeeper;
use \Onesimus\Router\Router;

Router::filter('auth', function() {
    if (GateKeeper::authenticated()) {
        return true;
    } else {
        View::redirect('login');
        exit();
    }
});
