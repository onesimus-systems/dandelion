<?php
/**
 * Setup routing for Dandelion
 */
namespace Dandelion;

Routes::group(['rprefix' => '\Dandelion\Controllers\\'], [
	['get', '/{page}', 'DefaultPageController@render'],
	['get', '/login', 'AuthController@loginPage'],
	['post', '/login', 'AuthController@login'],
	['get', '/logout', 'AuthController@logout'],
	['any', '/api/{module}/{method}', 'ApiController@apiCall']
]);

Routes::group(['rprefix' => '\Dandelion\Controllers\\', 'filter' => 'auth'], [
	['any', '/api/i/{module}/{method}', 'ApiController@internalApiCall'],
	// Temporary route for categories
	['any', '/lib/categories', 'DefaultPageController@categories']
]);
