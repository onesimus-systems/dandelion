<?php
/**
 * Handles API requests for Logs
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
namespace Dandelion\API;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(makeDAPI(2, 'This script can only be called by the API.', 'logs'));
}

class logsAPI
{
    /**
     * Grab JSON array of logs
     *
     * @return JSON
     */
    public static function read($db) {
        $rights = new \Dandelion\rights(USER_ID);

        if ($rights->authorized('viewlog')) {
            $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 25;
            $offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;

            $logs = new \Dandelion\logs();
            return $logs->getJSON($db, $limit, $offset);
        }
        else {
            exit(makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }
    }
}
