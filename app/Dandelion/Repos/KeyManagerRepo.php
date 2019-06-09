<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Repos;

use Dandelion\Repos\Interfaces;

class KeyManagerRepo extends BaseRepo implements Interfaces\KeyManagerRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'apikey';
    }

    private function fixApiKeyFieldTypes(&$record)
    {
        if (is_null($record)) { return; }
        $record['id'] = (int) $record['id'];
        $record['user_id'] = (int) $record['user_id'];
        $record['expires'] = (int) $record['expires'];
        $record['disabled'] = (bool) $record['disabled'];
    }

    public function getKeyForUser($uid)
    {
        $key = $this->database
            ->find($this->table)
            ->whereEqual('user_id', $uid)
            ->readRecord();

        if ($key) {
            $this->fixApiKeyFieldTypes($key);
        }
        return $key;
    }

    public function saveKeyForUser($uid, $key)
    {
        return $this->database
            ->createItem($this->table, ['keystring' => $key, 'user_id' => $uid]);
    }

    public function revoke($uid)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('user_id', $uid)
            ->delete();
    }
}
