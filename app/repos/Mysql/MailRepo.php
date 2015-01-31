<?php
/**
 * MySQL repository for Mail module
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class MailRepo extends BaseMySqlRepo implements Interfaces\MailRepo
{
    public function getUserMailCount($uid)
    {
        $this->database->select('mailCount')
                       ->from($this->prefix.'users')
                       ->where('userid = :id');

        return $this->database->getFirst(['id' => $uid])['mailCount'];
    }

    public function incrementUserMailCount($uid)
    {
        $this->database->update($this->prefix.'users')
                       ->set('mailCount = mailCount + 1')
                       ->where('userid = :id');

        return $this->database->go(['mc' => $mc, 'id' => $uid]);
    }

    public function updateUserMailCount($mc, $uid)
    {
        $this->database->update($this->prefix.'users')
                       ->set('mailCount = :mc')
                       ->where('userid = :id');

        return $this->database->go(['mc' => $mc, 'id' => $uid]);
    }

    public function getMailList($uid, $toFrom, $unreadCond)
    {
        $this->database->select('m.id, m.isItRead, m.subject, m.fromUser, m.dateSent, u.realname')
                       ->from($this->prefix.'mail AS m
                             LEFT JOIN '.$this->prefix.'users AS u
                                 ON m.fromUser = u.userid')
                       ->where('m.'.$toFrom.' = :id'.$unreadCond.' AND deleted = 0');

        return $this->database->get(['id' => $uid]);
    }

    public function getFullMailInfo($mid)
    {
        $this->database->select('m.*, u.realname')
                       ->from($this->prefix.'mail AS m
                             LEFT JOIN '.$this->prefix.'users AS u
                                 ON m.fromUser = u.userid')
                       ->where('id = :mid');

        return $this->database->get(['mid' => $mid]);
    }

    public function getUserList()
    {
        $this->database->select('userid, realname')
                       ->from($this->prefix.'users')
                       ->orderBy('realname');

        return $this->database->get();
    }

    public function getTrashCan($uid)
    {
        $this->database->select('m.*, u.realname')
                       ->from($this->prefix.'mail AS m
                            LEFT JOIN '.$this->prefix.'users AS u
                                ON m.fromUser = u.userid')
                       ->where('m.toUser = :id AND m.deleted = 1');

        return $this->database->get(['id' => $uid]);
    }

    public function addMailItem($to, $from, $subject, $body, $date, $time)
    {
        $this->database->insert()
                       ->into($this->prefix.'mail', ['subject','body','toUser','fromUser','dateSent','timeSent'])
                       ->values([':sub', ':body', ':to', ':from', ':date', ':time']);
        $params = [
            'sub' => $subject,
            'body' => $body,
            'to' => $to,
            'from' => $from,
            'date' => $date,
            'time' => $time
        ];
        return $this->database->go($params);
    }

    public function setReadMail($mid)
    {
        $this->database->update($this->prefix.'mail')
                       ->set('isItRead = 1')
                       ->where('id = :id');

        return $this->database->go(['id' => $mid]);
    }

    public function hardDeleteMail($mid)
    {
        $this->database->delete()
                       ->from($this->prefix.'mail')
                       ->where('id = :mid');

        return $this->database->go(['mid' => $mid]);
    }

    public function softDeleteMail($mid)
    {
        $this->database->update($this->prefix.'mail')
                       ->set('deleted = 1')
                       ->where('id = :mid');

        return $this->database->go(['mid' => $mid]);
    }
}
