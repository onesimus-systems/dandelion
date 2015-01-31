<?php
/**
 * Internal mail system
 */
namespace Dandelion\Mail;

use \Dandelion\Repos\Interfaces\MailRepo;

class mail
{
    public function __construct(MailRepo $repo)
    {
        $this->repo = $repo;
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
            $this->repo->updateUserMailCount($mailCount, $_SESSION['userInfo']['userid']);
        } else {
            $mailCount = $this->repo->getUserMailCount($_SESSION['userInfo']['userid']);
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

        return $this->repo->getMailList($_SESSION['userInfo']['userid'], $toFrom, $unreadCond);
    }

    /**
     * Get information for a single piece of mail
     */
    public function getFullMailInfo($mailId)
    {
        return $this->repo->getFullMailInfo($mailId);
    }

    /**
     * Get list of users as recipients
     */
    public function getUserList()
    {
        return $this->repo->getUserList();
    }

    /**
     * Get list of mail marked as trash
     */
    public function getTrashCan()
    {

        return $this->repo->getTrashCan($_SESSION['userInfo']['userid']);
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

            if ($this->repo->addMailItem($to, $from, $subject, $body, $new_date, $new_time)
                && $this->repo->incrementUserMailCount($to)) {
                $response = 'Mail sent successfully';
            } else {
                $response = 'There was an error sending your mail';
            }
        } else {
            $response = 'Error: You need a subject, body, and recipient.';
        }

        return $response;
    }

    /**
     * Set mail as read
     */
    public function setReadMail($mid)
    {
        return $this->repo->setReadMail($mid);
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
        } else {
            if ($trueDelete) {
                $ok = $this->repo->hardDeleteMail($mailId);
            } else {
                $ok = $this->repo->softDeleteMail($mailId);
            }

            return $ok ? 'Mail Deleted' : 'An error occured';
        }
        return '';
    }
}
