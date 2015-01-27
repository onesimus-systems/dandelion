<?php
namespace Dandelion\Controllers;

use \Dandelion\View;
use \Dandelion\Rights;
use \Dandelion\Utils\View as Vutils;
use \Dandelion\Categories;
use \Dandelion\Template;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\Auth\GateKeeper;
use \Dandelion\Storage\MySqlDatabase;
use \League\Plates\Engine;

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

        //$this->showPage($page);
        $this->showTemplate($page);
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

    public function showTemplate($page)
    {
        if (GateKeeper::authenticated()) {
            $userRights = new Rights($_SESSION['userInfo']['userid']);
        }

        $templates = new Engine($this->app->paths['app'].'/templates');
        $templates->addFolder('layouts', $this->app->paths['app'].'/templates/layouts');
        $templates->registerFunction('getCssSheets', function($sheets = []) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadCssSheets'), $sheets);
        });

        $templates->registerFunction('loadJS', function($js) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadJS'), $js);
        });

        $templates->registerFunction('getThemeList', function() {
            return Vutils::getThemeList();
        });

        $data = array(
            'appTitle' => $this->app->config['appTitle'],
            'tagline' => $this->app->config['tagline'],
            'appVersion' => Application::VERSION,
            'pageTitle' => ucfirst($page),
            'cheestoEnabled' => $this->app->config['cheestoEnabled'],
            'publicApiEnabled' => $this->app->config['publicApiEnabled'],
            'userRights' => $userRights
        );

        $templates->addData($data);

        echo $templates->render($page);
    }
}
