<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Controllers;

use Dandelion\User;
use Dandelion\Application;
use Dandelion\Session\SessionManager as Session;
use Dandelion\Factories\UserFactory;
use Dandelion\Auth\GateKeeper;

class BaseController
{
    // Instance of running application
    protected $app;
    protected $request;
    protected $rights;
    protected $sessionUser;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->request = $app->request;

        $uf = new UserFactory;
        $this->sessionUser = $uf->getWithKeycard(Session::get('userInfo')['id']);
    }

    protected function setResponse($body)
    {
        $this->app->response->setBody($body);
    }

    protected function authorized(User $user, $task)
    {
        return GateKeeper::authorized($user, $task);
    }
}
