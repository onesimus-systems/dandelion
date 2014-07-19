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

class mail extends Database\dbManage
{

    function checkNewMail($forced = false, $sent = false)
    {
        $mailCount = -255;
        
        if ($forced) {
            // Get number of unread mail items
            $mailItems = $this->getMailList($sent, true);
            $mailCount = count($mailItems);
            
            // Update user's mail count
            $sql = 'UPDATE ' . DB_PREFIX . 'users
                    SET mailCount = :mc
                    WHERE userid = :id';
            $params = array (
                'mc' => $mailCount,
                'id' => $_SESSION['userInfo']['userid'] 
            );
            
            $this->queryDB($sql, $params);
        }
        else {
            $sql = 'SELECT mailCount
                    FROM ' . DB_PREFIX . 'users
                    WHERE userid = :id';
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
        $unreadCond = ($unread) ? ' AND isItRead = 0' : '';
        
        $sql = 'SELECT m.id, m.isItRead, m.subject, m.fromUser, m.dateSent,
                    u.realname
                FROM ' . DB_PREFIX . 'mail AS m
                LEFT JOIN ' . DB_PREFIX . 'users AS u
                ON m.fromUser = u.userid
                WHERE m.' . $toFrom . ' = :id' . $unreadCond . ' AND deleted = 0';
        
        $params = array (
            'id' => $_SESSION['userInfo']['userid'] 
        );
        
        return $this->queryDB($sql, $params);
    }

    function getFullMailInfo($mailId)
    {
        $sql = 'SELECT m.*, u.realname
                FROM ' . DB_PREFIX . 'mail AS m
                LEFT JOIN ' . DB_PREFIX . 'users AS u
                ON m.fromUser = u.userid
                WHERE id = :mid';
        
        $params = array (
            'mid' => $mailId 
        );
        
        return $this->queryDB($sql, $params);
    }

    function getUserList()
    {
        $sql = 'SELECT userid, realname
                FROM ' . DB_PREFIX . 'users
                ORDER BY realname';
        
        return $this->queryDB($sql);
    }

    function getTrashCan()
    {
        $sql = 'SELECT m.*, u.realname
                FROM ' . DB_PREFIX . 'mail AS m
                LEFT JOIN '. DB_PREFIX.'users AS u
                ON m.fromUser = u.userid
                WHERE m.toUser = :id AND m.deleted = 1';
        
        $params = array (
            'id' => $_SESSION['userInfo']['userid'] 
        );
        
        return $this->queryDB($sql, $params);
    }

    function newMail($subject, $body, $to, $from)
    {
        if (!empty($subject) && !empty($body) && !empty($to) && !empty($from)) {
            $datetime = getdate();
            $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
            $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];
            
            $sql = 'INSERT INTO ' . DB_PREFIX . 'mail
                    (subject, body, toUser, fromUser, dateSent, timeSent)
                    VALUES (:sub, :body, :to, :from, :date, :time)';
            
            $params = array (
                'sub' => $subject,
                'body' => $body,
                'to' => $to,
                'from' => $from,
                'date' => $new_date,
                'time' => $new_time 
            );
            
            $this->queryDB($sql, $params);
            
            $sql = 'UPDATE ' . DB_PREFIX . 'users
                    SET mailCount = mailCount + 1
                    WHERE userid = :id';
            
            $params = array( 'id' => $to );
            
            $this->queryDB($sql, $params);
            
            $response = 'Mail sent successfully';
        }
        else {
            $response = 'Error: You need a subject, body, and recipient.';
        }
        
        return $response;
    }

    function setReadMail($mid)
    {
        $sql = 'UPDATE ' . DB_PREFIX . 'mail
                SET isItRead = 1
                WHERE id = :id';
        $params = array (
            'id' => $mid 
        );
        
        return $this->queryDB($sql, $params);
    }

    function deleteMail($mailId, $trueDelete = false)
    {
        if ($mailId === -1) {
            // Permanently delete ALL mail
            // Delete all mail for $user from _mail table
            // aka, empty trash
        }
        else {
            $sql = '';

            if ($trueDelete) {
                // Complete delete
                $sql = 'DELETE
                        FROM '. DB_PREFIX.'mail
                        WHERE id = :mid';
            }
            else {
                // Soft delete
                $sql = 'UPDATE ' . DB_PREFIX . 'mail
                        SET deleted = 1
                        WHERE id = :mid';
            }
                
            $params = array( 'mid' => $mailId );
            
            if ($this->queryDB($sql, $params)) {
                return 'Mail Deleted';
            }
            else {
                return 'An error occured';
            }
        }
    }
}