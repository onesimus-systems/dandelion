<?php
/**
 * MySQL repository for administration module
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class SessionRepo extends BaseMySqlRepo implements Interfaces\SessionRepo
{
    public function read($id)
    {
        $this->database->select('data')
            ->from($this->prefix . 'sessions')
            ->where('id = :sid');

        return $this->database->get(['sid' => $id]);
    }

    public function write($id, $data)
    {
        // Because of the complexity of this query, it is issued as a raw query
        $sql = "INSERT
                INTO " . $this->prefix . "sessions (id, data, last_accessed)
                VALUES (:id, :data, :time)
                ON DUPLICATE KEY
                    UPDATE
                    id = :id,
                    data = :data,
                    last_accessed = :time";
        $this->database->raw($sql);

        $params = array(
            'id'   => $id,
            'data' => $data,
            'time' => time()
        );

        return $this->database->go($params);
    }

    public function destroy($id)
    {
        $this->database->delete()
            ->from($this->prefix . 'sessions')
            ->where('id = :id');

        return $this->database->go(['id' => $id]);
    }

    public function gc($maxlifetime)
    {
        $this->database->delete()
            ->from($this->prefix . 'sessions')
            ->where('last_accessed + :maxlifetime < :time');

        $params = array(
            'maxlifetime' => $maxlifetime,
            'time'        => time()
        );

        return $this->database->go($params);
    }
}
