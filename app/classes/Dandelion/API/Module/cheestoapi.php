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

class cheestoAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    /**
     * Grab JSON array of all cheesto users and statuses
     *
     * @return JSON
     */
    public function readall() {
        if ($this->ur->authorized('viewcheesto')) {
            $cheesto = new \Dandelion\cxeesto($this->db);
            return json_encode($cheesto->getAllStatuses());
        }
        else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }
    }

    public function read() {
        if (!$this->ur->authorized('viewcheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new \Dandelion\cxeesto($this->db);
        return json_encode($cheesto->getUserStatus($this->up->uid));
    }

    public function statusTexts() {
        $cheesto = new \Dandelion\cxeesto($this->db);
        return json_encode($cheesto->getStatusText());
    }

    /**
     * Update the status of user
     *
     * @return JSON
     */
    public function update() {
        if (!$this->ur->authorized('updatecheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new \Dandelion\cxeesto($this->db);
        $message = $this->up->message;
        $status = $this->up->get('status', -1);
        $returntime = $this->up->get('returntime', '00:00:00');
        $userid = USER_ID;

        if (isset($this->up->uid)) { // A status of another user is trying to be updated
            if ($this->ur->authorized('edituser') || $this->up->uid == USER_ID) {
                $userid = $this->up->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
            }
        }

        return $cheesto->updateStatus($message, $status, $returntime, $userid);
    }
}
