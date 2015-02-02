<?php
/**
 * Filters used in routing
 */
namespace Dandelion;

use \Dandelion\Utils\View;
use \Dandelion\Auth\GateKeeper;

Routes::filter('auth', function() {
	if (GateKeeper::authenticated()) {
		return true;
	} else {
		View::redirect('login');
	}
});
