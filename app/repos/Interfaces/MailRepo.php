<?php
/**
 * Inteface for Mail repository
 */
namespace Dandelion\Repos\Interfaces;

interface MailRepo
{
    public function getUserMailCount($uid);
    public function incrementUserMailCount($uid);
    public function updateUserMailCount($mc, $uid);
    public function getMailList($uid, $toFrom, $unreadCond);
    public function getFullMailInfo($mid);
    public function getUserList();
    public function getTrashCan($uid);
    public function addMailItem($to, $from, $subject, $body, $date, $time);
    public function setReadMail($mid);
    public function hardDeleteMail($mid);
    public function softDeleteMail($mid);
}
