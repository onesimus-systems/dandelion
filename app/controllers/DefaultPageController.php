<?php
namespace Dandelion\Controllers;

use \Dandelion\View;
use \Dandelion\Rights;
use \Dandelion\Utils\View as Vutils;
use \Dandelion\Categories;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\Auth\GateKeeper;
use \Dandelion\Storage\MySqlDatabase;

class DefaultPageController
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function render($page = '')
    {
        $page = $page ?: 'dashboard';

        if (!GateKeeper::authenticated()) {
            Vutils::redirect('login');
            return;
        }

        $this->showPage($page);
        return;
    }

    public function categories()
    {
        $urlParams = new UrlParameters();

        if ($urlParams->action == 'grabcats') {
            $past = json_decode(stripslashes($urlParams->pastSelections));
            $displayCats = new Categories(MySqlDatabase::getInstance());
            echo $displayCats->getChildren($past);
        }
        return;
    }

    public function showPage($page)
    {
        if (GateKeeper::authenticated()) {
            $userRights = new Rights($_SESSION['userInfo']['userid']);
        }

        $template = new View();
        $template->setTemplatesDirectory($this->app->paths['app'].'/templates');
        $template->display($page.'.php', array(
            'paths' => array(
                'app' => $this->app->paths['app']
                ),
            'userRights' => $userRights,
            'appTitle' => $this->app->config['appTitle'],
            'tagline' => $this->app->config['tagline'],
            'pageTitle' => ucfirst($page),
            'cheestoEnabled' => $this->app->config['cheestoEnabled'],
            'publicApiEnabled' => $this->app->config['publicApiEnabled']
            )
        );
    }
}
