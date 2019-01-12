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

use Dandelion\Template;
use Dandelion\Utils\View;
use Dandelion\Auth\GateKeeper;

class AuthController extends BaseController
{
    public function loginPage()
    {
        if (GateKeeper::authenticated()) {
            View::redirect('dashboard');
            return;
        }

        $template = new Template($this->app);
        $this->setResponse($template->render('login', 'Login'));
    }

    public function login()
    {
        $auth = new GateKeeper();
        $rem = $this->request->postParam('remember') == 'true' ? true : false;

        $tryAuth = $auth->login($this->request->postParam('user'), $this->request->postParam('pass'), $rem);
        if (!$tryAuth) {
            $this->app->logger->info("Failed login attempt by user '{user}'", ['user' => $this->request->postParam('user')]);
        }

        $this->setResponse(json_encode($tryAuth));
        return;
    }

    public function logout()
    {
        GateKeeper::logout();
        View::redirect('login');
    }
}
