<?php
/**
 * Setup routing for Dandelion
 */
namespace Dandelion;

Routes::get('/{page}', '\Dandelion\Controllers\DefaultPageController@render');

Routes::get('/tutorial', '\Dandelion\Controllers\DefaultPageController@tutorial');
Routes::get('/login', '\Dandelion\Controllers\AuthController@loginPage');
Routes::post('/login', '\Dandelion\Controllers\AuthController@login');
Routes::get('/logout', '\Dandelion\Controllers\AuthController@logout');

Routes::any(
    '/api/i/{module}/{method}',
    array(
        'before' => 'auth',
        'use' => '\Dandelion\Controllers\ApiController@internalApiCall'
    ));

Routes::any('/api/{module}/{method}', '\Dandelion\Controllers\ApiController@apiCall');

// Temporary route for categories
Routes::any('/lib/categories', '\Dandelion\Controllers\DefaultPageController@categories');
