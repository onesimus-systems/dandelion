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

use Dandelion\API\ApiController;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(ApiController::makeDAPI(2, 'This script can only be called by the API.', 'admin'));
}

class adminAPI
{
    /**
     * Save the website tagline
     */
    public static function saveSlogan($db, $ur, $params) {
        return self::go($db, $ur, "saveSlogan", $params->data);
    }

    /**
     * Call DB backup function
     */
    public static function backupDB($db, $ur, $params) {
        return self::go($db, $ur, "backupDB", $params->data);
    }

    /**
     * Save the default theme for the site
     */
    public static function saveDefaultTheme($db, $ur, $params) {
        return self::go($db, $ur, "saveDefaultTheme", $params->data);
    }

    /**
     * Save Cheesto enabled state
     */
    public static function saveCheesto($db, $ur, $params) {
        return self::go($db, $ur, "saveCheesto", $params->data);
    }

    /**
     * Save public API enabled status
     */
    public static function savePAPI($db, $ur, $params) {
        return self::go($db, $ur, "savePAPI", $params->data);
    }

    /**
     * Perform administartor action
     */
    private static function go($db, $ur, $func, $data) {
        if ($ur->isAdmin()) {
            $action = new \Dandelion\adminactions($db);
            $response = $action->$func($data);
            return json_encode($response);
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'admin'));
        }
        return;
    }
}
