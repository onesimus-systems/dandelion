<?php
/**
 * Controller for authentication in Dandelion
 *
 * The DAPI array will always contain an error code. Please refer
 * to the documentation on the website or in the makeDAPI() function
 * for code meanings.
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
namespace Dandelion\Controllers;

use \Dandelion\UrlParameters;
use \Dandelion\Auth\GateKeeper;
use \Dandelion\Storage\MySqlDatabase;

class AuthController
{
    private $db;
    private $up;

    public function __construct() {
        $this->db = MySqlDatabase::getInstance();
        $this->up = new \Dandelion\UrlParameters();
    }

    public function login()
    {
        $auth = new GateKeeper($this->db);
        $rem = false;
        if ($this->up->remember == 'true') {
            $rem = true;
        }
        echo json_encode($auth->login($this->up->user, $this->up->pass, $rem));
        return;
    }

    public function logout()
    {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        \Dandelion\redirect('index');
    }
}
