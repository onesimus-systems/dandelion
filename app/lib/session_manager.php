<?php
/**
 * Functions to manage PHP sessions manually with database
 *
 * @author Lee Keitel
 * @date July, 2014
 *        
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 *         
 */
namespace Dandelion;

class SessionSaveHandler {
    protected $sessionName;
    protected $dbc;

    public function __construct() {
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
    }

    public function open($savePath, $sessionName) {
        $this->sessionName = $sessionName;
        $this->dbc = \Dandelion\Storage\mySqlDatabase::getInstance();
        return true;
    }

    public function close() {
        $this->gc(ini_get('session.gc_maxlifetime'));
        unset($this->dbc);
        return true;
    }

    public function read($id) {
        $this->dbc->select('data')
                  ->from(DB_PREFIX.'sessions')
                  ->where('id = :sid');
    
        $params = array(
            'sid' => $id 
        );
        
        $r = $this->dbc->get($params);
        
        if (count($r) == 1) {
            $data = $r[0]["data"];
            return $data;
        }
        else {
            return '';
        }
    }

    public function write($id, $data) {
        // Because of the complexity of this query, it is issued as a raw query
        $sql = "INSERT
                INTO " . DB_PREFIX . "sessions (id, data, last_accessed)
                VALUES (:id, :data, :time)
                ON DUPLICATE KEY
                    UPDATE
                    id = :id,
                    data = :data,
                    last_accessed = :time";
        $this->dbc->raw($sql);
    
        $params = array(
            'id' => $id,
            'data' => $data,
            'time' => time()
        );
        
        $this->dbc->go($params);
        return true;
    }

    public function destroy($id) {
        $this->dbc->delete()
                  ->from(DB_PREFIX.'sessions')
                  ->where('id = :id');
        
        $params = array(
            'id' => $id 
        );
        
        $this->dbc->go($params);
        
        $_SESSION = array();
        return true;
    }

    public function gc($maxlifetime) {
        $this->dbc->delete()
                  ->from(DB_PREFIX.'sessions')
                  ->where('last_accessed + :maxlifetime < :time');
        
        $params = array(
            'maxlifetime' => $maxlifetime,
            'time' => time()
        );
        
        $this->dbc->go($params);
        return true;
    }
}
