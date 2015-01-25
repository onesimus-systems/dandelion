<?php
/**
 * Handles authentication API requests
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
 * @date July 2014
 */
namespace Dandelion\API\Module;

use Dandelion\Controllers\ApiController;

class authAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    /**
     * Attempt to login user
     *
     * @return JSON
     */
    public function login() {
        $auth = new \Dandelion\Auth\GateKeeper($this->db);
        $rem = false;
        if ($this->up->remember == 'true') {
            $rem = true;
        }
        return json_encode($auth->login(urldecode($this->up->user), urldecode($this->up->pass), $rem));
    }
}
