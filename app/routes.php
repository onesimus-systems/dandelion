<?php
/**
 * Setup routing for Dandelion
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date Jan 2015
 */
namespace Dandelion;

Routes::get('/{page}', '\Dandelion\Controllers\DefaultPageController@render');
Routes::get('/logout', '\Dandelion\Auth\GateKeeper@logout');

Routes::any('/api/i/{module}/{method}', array('before' => 'auth',
        'use' => '\Dandelion\Controllers\ApiController@internalApiCall'));

Routes::any('/api/{module}/{method}', '\Dandelion\Controllers\ApiController@apiCall');
// Temporary route for categories
Routes::any('/lib/categories', '\Dandelion\Controllers\DefaultPageController@categories');
