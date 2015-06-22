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

use Dandelion\Categories;
use Dandelion\UrlParameters;
use Dandelion\Utils\Repos;

class RenderController extends BaseController
{

    public function render($item)
    {
        echo $this->$item();
    }

    /**
     * Returns JSON with list of categories from a category string
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
