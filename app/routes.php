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

// Available routes without prior authentication
Routes::group(['rprefix' => '\Dandelion\Controllers\\'], [
	['get', '/login', 'AuthController@loginPage'],
	['post', '/login', 'AuthController@login'],
	['get', '/logout', 'AuthController@logout'],

	['any', '/api/{module}/{method}', 'ApiController@apiCall'],
	['any', '/api/{module}', 'ApiController@badApiCall'],
	['any', '/api', 'ApiController@badApiCall']
]);

// Authentication required for these routes
Routes::group(['rprefix' => '\Dandelion\Controllers\\', 'filter' => 'auth'], [
	['get', '/', 'DashboardController@dashboard'],
	['get', '/dashboard', 'DashboardController@dashboard'],
	['get', '/{page}', 'PageController@render'],
	['get', '/settings', 'SettingsController@settings'],

	['any', '/api/i/{module}/{method}', 'ApiController@internalApiCall'],
	['any', '/api/i/{module}', 'ApiController@badApiCall'],
	['any', '/api/i', 'ApiController@badApiCall'],

	['any', '/render/{item}', 'RenderController@render']
]);

// Group for Administration pages, requires authentication
Routes::group([
	'prefix' => '/admin', // All admin pages begin with /admin/{something}
	'rprefix' => '\Dandelion\Controllers\AdminController',
	'filter' => 'auth'], [
	['get', '', '@admin'],
	['get', '/edituser/{uid}', '@editUser'],
	['get', '/edituser', '@editUser'],
	['get', '/editgroup/{gid}', '@editGroup'],
	['get', '/editgroup', '@editGroup']
]);
