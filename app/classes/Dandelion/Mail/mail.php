<?php
/**
 * Internal mail system
 */
namespace Dandelion\Mail;

use \Dandelion\Storage\Contracts\DatabaseConn;

class mail
{
    public function __construct(DatabaseConn $db)
    {
        $this->dbConn = $db;
        return;
    }

    /**
     * Check for any new mail
     */
    public function checkNewMail($forced = false, $sent = false)
    {
        $mailCount = -255;

        if ($forced) {
            // Get number of unread mail items
            $mailItems = $this->getMailList($sent, true);
            $mailCount = count($mailItems);

            // Update user's mail count
            $this->dbConn->update(DB_PREFIX.'users')
                          ->set('mailcount = :mc')
                          ->where('userid = :id');
            $params = array (
                'mc' => $mailCount,
                'id' => $_SESSION['userInfo']['userid']
            );
            $this->dbConn->go($params);
        }
        else {
            $this->dbConn->select('mailCount')
                          ->from(DB_PREFIX.'users')
                          ->where('userid = :id');
            $params = array (
                'id' => $_SESSION['userInfo']['userid']
            );
            $mailCount = $this->dbConn->getFirst($params)['mailCount'];
        }

        return (int) $mailCount;
    }

    /**
     * Get list of all mail items
     */
    public function getMailList($sent = false, $unread = false)
    {
        $toFrom = ($sent) ? 'fromUser' : 'toUser';
        $unreadCond = ($unread) ? ' AND isItRead = 0' : '';

        $this->dbConn->select('m.id, m.isItRead, m.subject, m.fromUser, m.dateSent, u.realname')
                      ->from(DB_PREFIX.'mail AS m LEFT JOIN '.DB_PREFIX.'users AS u ON m.fromUser = u.userid')
                      ->where('m.'.$toFrom.' = :id'.$unreadCond.' AND deleted = 0');
        $params = array (
            'id' => $_SESSION['userInfo']['userid']
        );
        return $this->dbConn->get($params);
    }

    /**
     * Get information for a single piece of mail
     */
    public function getFullMailInfo($mailId)
    {
        $this->dbConn->select('m.*, u.realname')
                      ->from(DB_PREFIX.'mail AS m LEFT JOIN '.DB_PREFIX.'users AS u ON m.fromUser = u.userid')
                      ->where('id = :mid');
        $params = array (
            'mid' => $mailId
        );
        return $this->dbConn->get($params);
    }

    /**
     * Get list of users as recipients
     */
    public function getUserList()
    {
        $this->dbConn->select('userid, realname')
                      ->from(DB_PREFIX.'users')
                      ->orderBy('realname');
        return $this->dbConn->get();
    }

    /**
     * Get list of mail marked as trash
     */
    public function getTrashCan()
    {
        $this->dbConn->select('m.*, u.realname')
                      ->from(DB_PREFIX.'mail AS m LEFT JOIN '.DB_PREFIX.'users AS u ON m.fromUser = u.userid')
                      ->where('m.toUser = :id AND m.deleted = 1');
        $params = array (
            'id' => $_SESSION['userInfo']['userid']
        );

        return $this->dbConn->get($params);
    }

    /**
     * Send new mail to user
     */
    public function newMail($subject, $body, $to, $from)
    {
        if (!empty($subject) && !empty($body) && !empty($to) && !empty($from)) {
            $datetime = getdate();
            $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
            $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];

            $this->dbConn->insert()->into(DB_PREFIX.'mail', array('subject','body','toUser','fromUser','dateSent','timeSent'))
                          ->values(array(':sub', ':body', ':to', ':from', ':date', ':time'));
            $params = array (
                'sub' => $subject,
                'body' => $body,
                'to' => $to,
                'from' => $from,
                'date' => $new_date,
                'time' => $new_time
            );
            $this->dbConn->go($params);

            $this->dbConn->update(DB_PREFIX.'users')
                          ->set('mailCount = mailCount + 1')
                          ->where('userid = :id');
            $params = array( 'id' => $to );
            $this->dbConn->go($params);

            $response = 'Mail sent successfully';
        }
        else {
            $response = 'Error: You need a subject, body, and recipient.';
        }

        return $response;
    }

    /**
     * Set mail as read
     */
    public function setReadMail($mid)
    {
        $this->dbConn->update(DB_PREFIX.'mail')
                      ->set('isItRead = 1')
                      ->where('id = :id');
        $params = array (
            'id' => $mid
        );
        return $this->dbConn->go($params);
    }

    /**
     * Delete a piece of mail
     */
    public function deleteMail($mailId, $trueDelete = false)
    {
        if ($mailId === -1) {
            // Permanently delete ALL mail
            // Delete all mail for $user from _mail table
            // aka, empty trash
        }
        else {
            if ($trueDelete) {
                // Complete delete
                $this->dbConn->delete()
                              ->from(DB_PREFIX.'mail')
                              ->where('id = :mid');
            }
            else {
                // Soft delete
                $this->dbConn->update(DB_PREFIX.'mail')
                              ->set('deleted = 1')
                              ->where('id = :mid');
            }

            $params = array( 'mid' => $mailId );

            if ($this->dbConn->go($params)) {
                return 'Mail Deleted';
            }
            else {
                return 'An error occured';
            }
        }
    }
}
