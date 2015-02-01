<?php
/**
 * Controller for rendering bits of data for the frontend
 */
namespace Dandelion\Controllers;

use \Dandelion\Categories;
use \Dandelion\UrlParameters;
use \Dandelion\Utils\Repos;

class RenderController extends BaseController
{

    public function render($item)
    {
        echo $this->$item();
    }

    /**
     * Renders select boxes from a string of category names. Used when editing a log.
     */
    public function editcat()
    {
        $urlParams = new UrlParameters();

        if ($urlParams->catstring) {
            $catRepo = Repos::makeRepo($this->app->config['db']['type'], 'Categories');
            $displayCats = new Categories($catRepo);
            echo $displayCats->renderFromString($urlParams->catstring);
        }
        return;
    }

    /**
     * Renders select boxes for an array of categories in the format '[category id]:[position]'
     */
    public function categories()
    {
        $urlParams = new UrlParameters();

        if ($urlParams->action == 'grabcats') {
            $past = json_decode(stripslashes($urlParams->pastSelections));
            $catRepo = $catRepo = Repos::makeRepo($this->app->config['db']['type'], 'Categories');
            $displayCats = new Categories($catRepo);
            echo $displayCats->renderChildren($past);
        }
        return;
    }
}
