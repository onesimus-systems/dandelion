<?php
/**
 * Dandelion session management
 */
namespace Dandelion\Session;

use Dandelion\Application;

class SessionManager implements \SessionHandlerInterface
{
    private $sessionName;
    private $timeout;
    private $gcLotto;
    private $repo;
    private $app;

    private static $instance;
    public static $session = [];

    private function __construct() {}
    private function __clone() {}

    public static function get($name)
    {
        return self::$session[$name];
    }

    public static function set($name, $value)
    {
        self::$session[$name] = $value;
        return;
    }

    public static function register(Application $app)
    {
        if (self::$instance === null) {
            self::$instance = new self();
        } else {
            return;
        }

        self::$instance->timeout = $app->config['sessionTimeout'] * 60; // 6 hours
        self::$instance->app = $app;
        self::$instance->gcLotto = $app->config['gcLottery'];

        session_set_save_handler(self::$instance, true);
        return;
    }

    public static function startSession($name)
    {
        session_name($name);
        session_start();
        self::$session = $_SESSION;
        return;
    }

    public function open($savePath, $sessionName)
    {
        $this->sessionName = $sessionName;

        $dbtype = ucfirst($this->app->config['db']['type']);
        $repo = "\\Dandelion\\Repos\\{$dbtype}\\SessionRepo";
        $this->repo = new $repo();
        return true;
    }

    public function close()
    {
        $odds = $this->gcLotto[0];
        $max = $this->gcLotto[1];

        if (mt_rand(0, $max - 1) < $odds) {
            $this->gc($this->timeout);
        }

        unset($this->repo);
        return true;
    }

    public function read($id)
    {
        $r = $this->repo->read($id);

        if (count($r) == 1) {
            $data = $r[0]["data"];
            return $data;
        } else {
            return '';
        }
    }

    public function write($id, $data)
    {
        return $this->repo->write($id, $data);
    }

    public function destroy($id)
    {
        $this->repo->destroy($id);
        $_SESSION = array();
        return true;
    }

    public function gc($maxlifetime)
    {
        return $this->repo->gc($maxlifetime);
    }
}
