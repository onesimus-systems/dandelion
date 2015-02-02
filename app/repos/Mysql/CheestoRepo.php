<?php
/**
 * MySQL repository for the Cheesto module
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class CheestoRepo extends BaseMySqlRepo implements Interfaces\CheestoRepo
{
    public function getAllStatuses()
    {
        return $this->database->selectAll('presence')->get();
    }

    public function getUserStatus($uid)
    {
        $this->database->select()
                       ->from($this->prefix.'presence')
                       ->where('uid = :uid');

        return $this->database->getFirst(['uid' => $uid]);
    }

    public function updateStatus($uid, $status, $message, $return, $date)
    {
        $this->database->update($this->prefix.'presence')
                       ->set(['message = :message', 'status = :setorno', 'returntime = :returntime', 'dmodified = :dmodified'])
                       ->where('uid = :uid');
        $params = [
            'message' => $message,
            'setorno' => $status,
            'returntime' => $return,
            'dmodified' => $date,
            'uid' => $uid
        ];

        return $this->database->go($params);
    }
}
