<?php
namespace Dandelion\Controllers;

use \Dandelion\Categories;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\Auth\GateKeeper;
use \Dandelion\Storage\MySqlDatabase;

class DefaultPageController
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function render($page = '')
    {
        global $User_Rights;
        $page = $page ?: 'dashboard';
        $paths = $this->app->paths;

        // Load page
        $indexCall = true;
        if (is_file($paths['app'].'/pages/'.$page.'.php') && GateKeeper::authenticated()) {
            include $paths['app'].'/pages/'.$page.'.php';
        } else {
            include $paths['app'].'/pages/login.php';
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
}
