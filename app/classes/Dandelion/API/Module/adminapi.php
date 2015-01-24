<?php
/**
 * Handles API requests for Administrator Actions
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
 * @date December 2014
 */
namespace Dandelion\API\Module;

use Dandelion\Controllers\ApiController;

class adminAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    /**
     * Save the website tagline
     */
    public function saveSlogan() {
        return self::go("saveSlogan", $this->up->data);
    }

    /**
     * Call DB backup function
     */
    public function backupDB() {
        return self::go("backupDB", $this->up->data);
    }

    /**
     * Save the default theme for the site
     */
    public function saveDefaultTheme() {
        return self::go("saveDefaultTheme", $this->up->data);
    }

    /**
     * Save Cheesto enabled state
     */
    public function saveCheesto() {
        return self::go("saveCheesto", $this->up->data);
    }

    /**
     * Save public API enabled status
     */
    public function savePAPI() {
        return self::go("savePAPI", $this->up->data);
    }

    /**
     * Perform administartor action
     */
    private function go($func, $data) {
        if ($this->ur->isAdmin()) {
            $action = new \Dandelion\adminactions($this->db);
            $response = $action->$func($data);
            return json_encode($response);
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'admin'));
        }
        return;
    }
}
