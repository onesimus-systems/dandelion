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

use Dandelion\Utils\View;
use Dandelion\Utils\Repos;
use Dandelion\Application;
use Dandelion\Auth\GateKeeper;
use League\Plates\Engine;

class AuthController extends BaseController
{
    public function loginPage()
    {
        if (GateKeeper::authenticated()) {
            View::redirect('dashboard');
            return;
        }

        $templates = new Engine($this->app->paths['app'].'/templates');
        $templates->registerFunction('getCssSheets', function() {
            return View::loadCssSheets('jqueryui','login');
        });
        $templates->registerFunction('loadJS', function() {
            return View::loadJS('jquery','jqueryui','common','login');
        });

        $this->setResponse($templates->render('login'));
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
