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
    protected $sdbc;

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
        $this->sdbc = new \Dandelion\Database\dbManage();
        return true;
    }

    public function close() {
        $this->gc(ini_get('session.gc_maxlifetime'));
        unset($this->sdbc);
        return true;
    }

    public function read($id) {
        $sql = "SELECT data
                FROM " . DB_PREFIX . "sessions
                WHERE id=:sid";
    
        $params = array(
            'sid' => $id 
        );
        
        $r = $this->sdbc->queryDB($sql, $params);
        
        if (count($r) == 1) {
            $data = $r[0]["data"];
            return $data;
        }
        else {
            return '';
        }
    }

    public function write($id, $data) {
        $sql = "INSERT
                INTO " . DB_PREFIX . "sessions (id, data, last_accessed)
                VALUES (:id, :data, :time)
                ON DUPLICATE KEY
                    UPDATE
                    id = :id,
                    data = :data,
                    last_accessed = :time";
    
        $params = array(
            'id' => $id,
            'data' => $data,
            'time' => time()
        );
        
        $this->sdbc->queryDB($sql, $params);
        return true;
    }

    public function destroy($id) {
        $sql = "DELETE
                FROM " . DB_PREFIX . "sessions
                WHERE id=:id";
        
        $params = array(
            'id' => $id 
        );
        
        $this->sdbc->queryDB($sql, $params);
        
        $_SESSION = array();
        return true;
    }

    public function gc($maxlifetime) {
        $sql = "DELETE
                FROM " . DB_PREFIX . "sessions
                WHERE last_accessed + :maxlifetime < :time";
        
        $params = array(
            'maxlifetime' => $maxlifetime,
            'time' => time()
        );
        
        $this->sdbc->queryDB($sql, $params);
        return true;
    }
}
