<?php
namespace Dandelion\Controllers;

use \Dandelion\View;
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

        if (GateKeeper::authenticated()) {
            $this->showPage($page);
        } else {
            $this->showLogin();
        }

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
        global $User_Rights;
        $template = new View();
        $template->setTemplatesDirectory($this->app->paths['app'].'/pages');
        $template->display($page.'.php', array(
            'paths' => array(
                'app' => $this->app->paths['app']
                ),
            'User_Rights' => $User_Rights,
            'appTitle' => $this->app->config['appTitle'],
            'tagline' => $this->app->config['tagline'],
            'pageTitle' => ucfirst($page),
            'cheestoEnabled' => $this->app->config['cheestoEnabled']
            )
        );
    }

    public function showLogin()
    {
        $template = new View();
        $template->setTemplatesDirectory($this->app->paths['app'].'/pages');
        $template->display('login.php');
    }
}
