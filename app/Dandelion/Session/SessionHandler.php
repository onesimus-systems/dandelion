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

class SessionHandler implements \SessionHandlerInterface
{
    private $sessionName;
    private $timeout;
    private $gcLotto;
    private $repo;
    private $app;

    public function __construct(Application $app, $timeout, $gcLotto)
    {
        $this->app = $app;
        $this->gcLotto = $gcLotto;
        $this->timeout = $timeout;
    }

    public function open($savePath, $sessionName)
    {
        $this->sessionName = $sessionName;
        $repo = "\\Dandelion\\Repos\\SessionRepo";
        $this->repo = new $repo();

        // Garbage collection
        $odds = $this->gcLotto[0];
        $max = $this->gcLotto[1];

        if ($max < 1) {
            $max = 100;
        }

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
        SessionManager::clear();
        return;
    }

    public function gc($maxlifetime)
    {
        $this->app->logger->info('Executing session garbage collection...');
        return (bool) $this->repo->gc($maxlifetime);
    }
}
