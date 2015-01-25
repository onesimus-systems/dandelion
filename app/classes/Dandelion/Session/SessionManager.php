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
namespace Dandelion\Session;

use \Dandelion\Storage\MySqlDatabase;

class SessionManager
{
    private $sessionName;
    private $dbc;
    private static $instance;

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function register() {
        if (self::$instance === NULL) {
            self::$instance = new self();
        } else {
            return false;
        }

        $timeout = 21600; // 6 hours
        ini_set('session.gc_maxlifetime', $timeout);

        session_set_save_handler(
            array(self::$instance, "open"),
            array(self::$instance, "close"),
            array(self::$instance, "read"),
            array(self::$instance, "write"),
            array(self::$instance, "destroy"),
            array(self::$instance, "gc")
        );
        register_shutdown_function('session_write_close');
        return;
    }

    public static function startSession() {
        session_name(PHP_SESSION_NAME);
        session_start();
        return;
    }

    public function open($savePath, $sessionName) {
        $this->sessionName = $sessionName;
        $this->dbc = MySqlDatabase::getInstance();
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
