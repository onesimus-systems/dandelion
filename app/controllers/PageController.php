<?php
/**
 * Controller for simple pages such as tutorial and about
 */
namespace Dandelion\Controllers;

use \Dandelion\Template;
use \Dandelion\Categories;
use \Dandelion\UrlParameters;
use \Dandelion\Storage\MySqlDatabase;

class PageController extends BaseController
{

    public function render($page)
    {
        $template = new Template($this->app);
        $template->render($page);
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
