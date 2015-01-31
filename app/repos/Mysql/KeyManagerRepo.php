<?php
/**
 * Repository for API key manager
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class KeyManagerRepo extends BaseMySqlRepo implements Interfaces\KeyManagerRepo
{
    public function getKeyForUser($uid)
    {
        $this->database->select('keystring')
                       ->from($this->prefix.'apikeys')
                       ->where('user = :id');

        return $this->database->getFirst(['id' => $uid]);
    }

    public function deleteKeyForUser($uid)
    {
        $this->database->delete()
                       ->from($this->prefix.'apikeys')
                       ->where('user = :id');

        return $this->database->go(['id' => $uid]);
    }

    public function saveKeyForUser($uid, $key)
    {
        $this->database->insert()
                       ->into($this->prefix.'apikeys', ['keystring', 'user'])
                       ->values([':newkey', ':uid']);

        return $this->database->go(['newkey' => $key, 'uid' => $uid]);
    }

    public function revoke($uid)
    {
        $this->database->delete()
                       ->from($this->prefix.'apikeys')
                       ->where('user = :id');

        return $this->database->go(['id' => $uid]);
    }
}
