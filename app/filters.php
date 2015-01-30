<?php
/**
 * Filters used in routing
 */
namespace Dandelion;

use \Dandelion\Auth\GateKeeper;

Routes::filter('auth', function() {
	return GateKeeper::authenticated();
});
