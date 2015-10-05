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
use Dandelion\UrlParameters;
use Dandelion\Auth\GateKeeper;
use League\Plates\Engine;

class AuthController extends BaseController
{
    // Repo for authentication object
    private $repo;

    // URL parameters
    private $up;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->up = new UrlParameters();
    }

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
        $rem = $this->up->remember == 'true' ? true : false;

        $tryAuth = $auth->login($this->up->user, $this->up->pass, $rem);
        if (!$tryAuth) {
            $this->app->logger->info("Failed login attempt by user '{user}'", ['user' => $this->up->user]);
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
