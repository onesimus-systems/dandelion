<?php

/**
 * Handle internal mail system
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
 *         @date June 2014
 *         *
 */
namespace Dandelion\Mail;

use Dandelion\Database;

/**
 * Database schema:
 *
 * id - int
 * isRead - bool (0,1)
 * toUser - int (user ID)
 * fromUser - int (user ID)
 * subject - string
 * body - big string
 * deleted - bool (0,1) - Used for trash can functionality
 * dateSent - date
 * timeSent - time
 */
class mail extends Database\dbManage
{

    function checkNewMail($forced = false, $sent = false)
    {
        $mailCount = -255; // -255 is used to show indication of counter error
        
        if ($forced) {
            // Get number of unread mail items
            $mailItems = $this->getMailList($sent, true);
            $mailCount = count($mailItems);
            
            // Update user's mail count
            $sql = 'UPDATE ' . DB_PREFIX . 'users SET mailCount = :mc WHERE userid = :id';
            $params = array (
                'mc' => $mailCount,
                'id' => $_SESSION['userInfo']['userid'] 
            );
            
            $this->queryDB($sql, $params);
        }
        else {
            $sql = 'SELECT mailCount FROM ' . DB_PREFIX . 'users WHERE userid = :id';
            $params = array (
                'id' => $_SESSION['userInfo']['userid'] 
            );
            
            $mailCount = $this->queryDB($sql, $params)[0]['mailCount'];
        }
        
        return $mailCount;
    }

    function getMailList($sent = false, $unread = false)
    {
        $toFrom = ($sent) ? 'fromUser' : 'toUser';
        $unreadCond = ($unread) ? ' AND isRead = 0' : '';
        
        $sql = 'SELECT m.id, m.isRead, m.subject, m.fromUser, m.dateSent,
                    u.realname
                FROM ' . DB_PREFIX . 'mail AS m
                LEFT JOIN ' . DB_PREFIX . 'users AS u
                ON m.fromUser = u.userid
                WHERE m.' . $toFrom . ' = :id' . $unreadCond;
        
        $params = array (
            'id' => $_SESSION['userInfo']['userid'] 
        );
        
        return $this->queryDB($sql, $params);
    }

    function getFullMailInfo($mailId) {
        $sql = 'SELECT m.*, u.realname
                FROM ' . DB_PREFIX . 'mail AS m
                LEFT JOIN ' . DB_PREFIX . 'users AS u
                ON m.fromUser = u.userid
                WHERE id = :mid';
        
        $params = array( 'mid' => $mailId );
        
        return $this->queryDB($sql, $params);
    }
    
    function getTrashCan()
    {
        $sql = 'SELECT * FROM ' . DB_PREFIX . 'mail WHERE to = :id and deleted = 1';
        $params = array (
            'id' => $_SESSION['userInfo']['userid'] 
        );
        
        return $this->queryDB($sql, $params);
    }

    function newMail($subject, $body, $to, $from)
    {
        if (!empty($subject) && !empty($body) && !empty($to) && !empty($from)) {
            /**
             * Save mail to _mail table
             * Increment unread_mail of user $to by 1
             *
             * Maybe check to make sure it was saved?
             */
            $response = 'Mail sent successfully';
        }
        else {
            // error message requiring subject, body, to, and from
            // this will be checked on the client side, but we can't
            // trust user data
            $response = '';
        }
        
        return $response;
    }

    function deleteMail($mailId)
    {
        if ($mailId === -1) {
            // Permanently delete mail
            // Delete all mail for $user from _mail table
            // aka, empty trash
        }
        else {
            // Move to trashcan
            // Change deleted field to 1 (true) for id = $mailId
        }
    }
}