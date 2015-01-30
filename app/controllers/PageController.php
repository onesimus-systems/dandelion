<?php
namespace Dandelion\Controllers;

use \Dandelion\Categories;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\Storage\MySqlDatabase;
use \League\Plates\Engine;

class PageController extends BaseController
{

    public function render($page = '')
    {
        $templates = new Engine($this->app->paths['app'].'/templates');
        $templates->addFolder('layouts', $this->app->paths['app'].'/templates/layouts');
        $templates->registerFunction('getCssSheets', function($sheets = []) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadCssSheets'), $sheets);
        });

        $templates->registerFunction('loadJS', function($js) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadJS'), $js);
        });

        $data = array(
            'appTitle' => $this->app->config['appTitle'],
            'tagline' => $this->app->config['tagline'],
            'appVersion' => Application::VERSION,
            'pageTitle' => ucfirst($page)
        );

        $templates->addData($data);

        try {
            echo $templates->render($page);
        } catch (\Exception $e) {
            $this->showTemplate('error');
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
