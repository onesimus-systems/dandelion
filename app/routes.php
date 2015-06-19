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

use \Onesimus\Router\Router;

// Available routes without prior authentication
Router::group(['rprefix' => '\Dandelion\Controllers\\'], [
	['get', '/login', 'AuthController@loginPage'],
	['post', '/login', 'AuthController@login'],
	['get', '/logout', 'AuthController@logout'],

	['any', '/api/{?module}/{?method}', 'ApiController@apiCall']
]);

// Authentication required for these routes
Router::group(['rprefix' => '\Dandelion\Controllers\\', 'filter' => 'auth'], [
    ['get', '/{page}', 'PageController@render'],
	['get', '/', 'DashboardController@dashboard'],
	['get', '/dashboard', 'DashboardController@dashboard'],
	['get', '/settings', 'SettingsController@settings'],
    ['any', '/render/{item}', 'RenderController@render'],

	['any', '/api/i/{?module}/{?method}', 'ApiController@internalApiCall']
]);

// Group for Administration pages, requires authentication
Router::group([
	'prefix' => '/admin', // All admin pages begin with /admin/{something}
	'rprefix' => '\Dandelion\Controllers\AdminController',
	'filter' => 'auth'], [
	['get', '', '@admin'],
	['get', '/edituser/{?uid}', '@editUser'],
	['get', '/editgroup/{?gid}', '@editGroup']
]);

Router::register404Route('\Dandelion\Controllers\NotFoundController@render');
