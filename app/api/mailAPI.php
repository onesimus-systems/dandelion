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
namespace Dandelion\API;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(ApiController::makeDAPI(2, 'This script can only be called by the iAPI.', 'mail'));
}

class mailAPI
{
    /**
     * Grab JSON array of all cheesto users and statuses
     *
     * @return JSON
     */
    public static function read($db) {
        $myMail = new \Dandelion\Mail\mail($db);

        $mail = $myMail->getFullMailInfo($_REQUEST['mid']);
        $myMail->setReadMail($_REQUEST['mid']);

        return json_encode($mail);
    }

    public static function mailCount($db) {
        $myMail = new \Dandelion\Mail\mail($db);

        $count = $myMail->checkNewMail(true);
        $count = array( 'count' => $count);

        return json_encode($count);
    }

    public static function delete($db) {
        $myMail = new \Dandelion\Mail\mail($db);

        $perm = ($_REQUEST['permenant'] === 'true') ? true : false;
        $response = $myMail->deleteMail($_REQUEST['mid'], $perm);

        return json_encode($response);
    }

    public static function getUserList($db) {
        $myMail = new \Dandelion\Mail\mail($db);

        $toUsers = $myMail->getUserList();

        return json_encode($toUsers);
    }

    public static function getAllMail($db) {
        $myMail = new \Dandelion\Mail\mail($db);

        if (isset($_REQUEST['trash']) && $_REQUEST['trash']) {
            $mailItems = $myMail->getTrashCan();
        }
        else {
            $mailItems = $myMail->getMailList();
        }

        return json_encode($mailItems);
    }

    public static function send($db) {
        $myMail = new \Dandelion\Mail\mail($db);

        $piece = (array) json_decode($_REQUEST['mail']);
        $response = $myMail->newMail($piece['subject'], $piece['body'], $piece['to'], $_SESSION['userInfo']['userid']);

        return json_encode($response);
    }
}
