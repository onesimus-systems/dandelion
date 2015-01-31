<?php
/**
 * Controller for simple pages such as tutorial and about
 */
namespace Dandelion\Controllers;

use \Dandelion\Template;
use \Dandelion\Categories;
use \Dandelion\UrlParameters;
use \Dandelion\Repos\Mysql\CategoriesRepo;

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
            $catRepo = new CategoriesRepo();
            $displayCats = new Categories($catRepo);
            echo $displayCats->getChildren($past);
        }
        return;
    }
}
