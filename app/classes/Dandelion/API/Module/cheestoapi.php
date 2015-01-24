<?php
/**
 * Handles Cheesto API requests
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

use Dandelion\API\ApiController;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(ApiController::makeDAPI(2, 'This script can only be called by the API.', 'cheesto'));
}

class cheestoAPI
{
    /**
     * Grab JSON array of all cheesto users and statuses
     *
     * @return JSON
     */
    public static function readall($db, $ur, $params) {
        if ($ur->authorized('viewcheesto')) {
            $cheesto = new \Dandelion\cxeesto($db);
            return json_encode($cheesto->getAllStatuses());
        }
        else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }
    }

    public static function read($db, $ur, $params) {
        if (!$ur->authorized('viewcheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new \Dandelion\cxeesto($db);
        return json_encode($cheesto->getUserStatus($params->uid));
    }

    public static function statusTexts() {
        $cheesto = new \Dandelion\cxeesto($db);
        return json_encode($cheesto->getStatusText());
    }

    /**
     * Update the status of user
     *
     * @return JSON
     */
    public static function update($db, $ur, $params) {
        if (!$ur->authorized('updatecheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new \Dandelion\cxeesto($db);
        $message = $params->message;
        $status = $params->get('status', -1);
        $returntime = $params->get('returntime', '00:00:00');
        $userid = USER_ID;

        if (isset($params->uid)) { // A status of another user is trying to be updated
            if ($ur->authorized('edituser') || $params->uid == USER_ID) {
                $userid = $params->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
            }
        }

        return $cheesto->updateStatus($message, $status, $returntime, $userid);
    }
}
