<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
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

        self::$instance->timeout = $app->config['sessionTimeout'] * 60;
        self::$instance->app = $app;
        self::$instance->gcLotto = $app->config['gcLottery'];

        session_set_save_handler(self::$instance, true);
        return;
    }

    public static function startSession($name)
    {
        session_name($name);
        session_start();
        self::$session = &$_SESSION;
        return;
    }

    public function open($savePath, $sessionName)
    {
        $this->sessionName = $sessionName;
        $repo = "\\Dandelion\\Repos\\SessionRepo";
        $this->repo = new $repo();

        // Garbage collection
        $odds = $this->gcLotto[0];
        $max = $this->gcLotto[1];
        if (mt_rand(0, $max - 1) < $odds) {
            $this->gc($this->timeout);
        }
        return true;
    }

    public function close()
    {
        unset($this->repo);
        return true;
    }

    public function read($id)
    {
        $r = $this->repo->read($id);

        if (is_null($r)) {
            return '';
        } else {
            return $r;
        }
    }

    public function write($id, $data)
    {
        $this->repo->write($id, $data);
        return;
    }

    public function destroy($id)
    {
        $this->repo->destroy($id);
        $_SESSION = [];
        return;
    }

    public function gc($maxlifetime)
    {
        $this->app->logger->info('Executing session garbage collection...');
        return (bool) $this->repo->gc($maxlifetime);
    }
}
