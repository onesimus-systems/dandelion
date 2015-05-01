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
     * Returns JSON with list of categories at each level
     */
    public function editcat()
    {
        $urlParams = new UrlParameters();

        if ($urlParams->catstring) {
            $catRepo = Repos::makeRepo('Categories');
            $displayCats = new Categories($catRepo);
            echo $displayCats->renderFromString($urlParams->catstring);
        }
        return;
    }

    /**
     * Returns JSON with list of categories at each level
     */
    public function categoriesJson()
    {
        $urlParams = new UrlParameters();

        $past = json_decode(stripslashes($urlParams->pastSelection));
        $catRepo = $catRepo = Repos::makeRepo('Categories');
        $displayCats = new Categories($catRepo);
        echo $displayCats->renderChildrenJson($past);
        return;
    }
}
