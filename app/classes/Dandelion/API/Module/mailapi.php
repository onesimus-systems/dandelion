<?php
/**
 * Mail API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Mail\Mail;
use \Dandelion\Controllers\ApiController;

class MailAPI extends BaseModule
{
    /**
     * Grab JSON array of all cheesto users and statuses
     *
     * @return JSON
     */
    public function read()
    {
        $myMail = new Mail($this->repo);

        $mail = $myMail->getFullMailInfo($this->up->mid);
        $myMail->setReadMail($this->up->mid);

        return $mail;
    }

    /**
     * Get the number of unread messages
     */
    public function mailCount()
    {
        $myMail = new Mail($this->repo);

        $count = $myMail->checkNewMail(true);
        $count = array( 'count' => $count);

        return $count;
    }

    /**
     * Delete mail from mailbox
     */
    public function delete()
    {
        $myMail = new Mail($this->repo);

        $perm = ($this->up->permenant === 'true') ? true : false;
        $response = $myMail->deleteMail($this->up->mid, $perm);

        return $response;
    }

    /**
     * Get a user list of available recipients
     */
    public function getUserList()
    {
        $myMail = new Mail($this->repo);

        $toUsers = $myMail->getUserList();

        return $toUsers;
    }

    /**
     * Get the list of all mail for a user
     */
    public function getAllMail()
    {
        $myMail = new Mail($this->repo);

        if ($this->up->trash) {
            $mailItems = $myMail->getTrashCan();
        }
        else {
            $mailItems = $myMail->getMailList();
        }

        return $mailItems;
    }

    /**
     * Send a new piece of mail
     */
    public function send()
    {
        $myMail = new Mail($this->repo);

        $piece = json_decode($this->up->mail, true);
        $response = $myMail->newMail(
            $piece['subject'],
            $piece['body'],
            $piece['to'],
            $_SESSION['userInfo']['userid']
        );

        return $response;
    }
}
