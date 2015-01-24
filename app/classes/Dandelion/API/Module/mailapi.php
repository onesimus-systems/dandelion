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

class mailAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    /**
     * Grab JSON array of all cheesto users and statuses
     *
     * @return JSON
     */
    public function read() {
        $myMail = new \Dandelion\Mail\mail($this->db);

        $mail = $myMail->getFullMailInfo($this->up->mid);
        $myMail->setReadMail($this->up->mid);

        return json_encode($mail);
    }

    public function mailCount() {
        $myMail = new \Dandelion\Mail\mail($this->db);

        $count = $myMail->checkNewMail(true);
        $count = array( 'count' => $count);

        return json_encode($count);
    }

    public function delete() {
        $myMail = new \Dandelion\Mail\mail($this->db);

        $perm = ($this->up->permenant === 'true') ? true : false;
        $response = $myMail->deleteMail($this->up->mid, $perm);

        return json_encode($response);
    }

    public function getUserList() {
        $myMail = new \Dandelion\Mail\mail($this->db);

        $toUsers = $myMail->getUserList();

        return json_encode($toUsers);
    }

    public function getAllMail() {
        $myMail = new \Dandelion\Mail\mail($this->db);

        if ($this->up->trash) {
            $mailItems = $myMail->getTrashCan();
        }
        else {
            $mailItems = $myMail->getMailList();
        }

        return json_encode($mailItems);
    }

    public function send() {
        $myMail = new \Dandelion\Mail\mail($this->db);

        $piece = (array) json_decode($this->up->mail);
        $response = $myMail->newMail($piece['subject'], $piece['body'], $piece['to'], $_SESSION['userInfo']['userid']);

        return json_encode($response);
    }
}
