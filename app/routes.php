<?php
/**
 * Setup routing for Dandelion
 */
namespace Dandelion;

// Available routes without prior authentication
Routes::group(['rprefix' => '\Dandelion\Controllers\\'], [
	['get', '/login', 'AuthController@loginPage'],
	['post', '/login', 'AuthController@login'],
	['get', '/logout', 'AuthController@logout'],

	['any', '/api/{module}/{method}', 'ApiController@apiCall'],
	['any', '/api/{module}', 'ApiController@apiCall']
]);

// Authentication required for these routes
Routes::group(['rprefix' => '\Dandelion\Controllers\\', 'filter' => 'auth'], [
	['get', '/', 'DashboardController@dashboard'],
	['get', '/{page}', 'PageController@render'],
	['get', '/dashboard', 'DashboardController@dashboard'],
	['get', '/settings', 'SettingsController@settings'],

	['any', '/api/i/{module}/{method}', 'ApiController@internalApiCall'],
	['any', '/render/{item}', 'RenderController@render']
]);

// Group for Administration pages, requires authentication
Routes::group(['rprefix' => '\Dandelion\Controllers\AdminController', 'filter' => 'auth'], [
	['get', '/admin', '@admin'],
	['get', '/editusers', '@editUsers'],
	['get', '/editgroups', '@editGroups'],
	['get', '/categories', '@editCategories'],
]);
