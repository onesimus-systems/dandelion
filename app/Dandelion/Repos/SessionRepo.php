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

use \Dandelion\Repos\Interfaces;
use \SC\SCException;

class SessionRepo extends BaseRepo implements Interfaces\SessionRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'session';
    }

    public function read($id)
    {
        $session = $this->database->readItem($this->table, $id);
        return $session['data'];
    }

    public function write($id, $data)
    {
        $session = $this->read($id);

        if (!is_null($session)) {
            $this->database->updateItem($this->table, $id, ['data'=>$data, 'last_accessed'=>time()]);
        } else {
            $this->database->createItem($this->table, [
                'id'   => $id,
                'data' => $data,
                'last_accessed' => time()
            ]);
        }

        return;
    }

    public function destroy($id)
    {
        $this->database->deleteItem($this->table, $id);
    }

    public function gc($maxlifetime)
    {
        $this->database->find($this->table)
            ->where('last_accessed + ? < ?', [$maxlifetime, time()])
            ->delete();
    }
}
