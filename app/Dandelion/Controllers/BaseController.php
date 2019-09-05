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

abstract class BaseController
{
    // Instance of running application
    protected $app;
    protected $request;
    protected $rights;
    protected $sessionUser;

    public function __construct(Application $app, $getUser=true)
    {
        $this->app = $app;
        $this->request = $app->request;

        if ($getUser) {
            $this->sessionUser = (new UserFactory)->getWithKeycard(Session::get('userInfo')['id']);
        }

        $this->init();
    }

    protected function setResponse($body)
    {
        $this->app->response->setBody($body);
    }

    protected function setHttpCode($code)
    {
        $this->app->response->setStatus($code);
    }

    protected function authorized(User $user, $task)
    {
        return (GateKeeper::getInstance())->authorized($user->getKeycard(), $task);
    }

    protected function init() { }
}
