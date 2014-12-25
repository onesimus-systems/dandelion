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
namespace Dandelion\API;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(makeDAPI(2, 'This script can only be called by the API.', 'admin'));
}

class adminAPI
{
    /**
     * Save the website tagline
     */
    public static function saveSlogan($db)
    {
        return self::go($db, "saveSlogan", $_REQUEST['data']);
    }

    /**
     * Call DB backup function
     */
    public static function backupDB($db)
    {
        return self::go($db, "backupDB", $_REQUEST['data']);
    }

    /**
     * Save the default theme for the site
     */
    public static function saveDefaultTheme($db)
    {
        return self::go($db, "saveDefaultTheme", $_REQUEST['data']);
    }

    /**
     * Save Cheesto enabled state
     */
    public static function saveCheesto($db)
    {
        return self::go($db, "saveCheesto", $_REQUEST['data']);
    }

    /**
     * Save public API enabled status
     */
    public static function savePAPI($db) {
        return self::go($db, "savePAPI", $_REQUEST['data']);
    }

    /**
     * Perform administartor action
     */
    private static function go($db, $func, $data) {
        $rights = new \Dandelion\rights(USER_ID);
        if ($rights->isAdmin()) {
            $action = new \Dandelion\adminactions($db);
            $response = $action->$func($data);
            return json_encode($response);
        } else {
            exit(makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'admin'));
        }
        return;
    }
}
