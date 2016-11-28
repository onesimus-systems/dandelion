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
use Dandelion\Utils\Repos;

class RenderController extends BaseController
{

    public function render($item)
    {
        $this->$item();
    }

    /**
     * Returns JSON with list of categories from a category string
     */
    public function editcat()
    {
        if ($this->request->postParam('catstring')) {
            $catRepo = Repos::makeRepo('Categories');
            $displayCats = new Categories($catRepo);
            $this->setResponse($displayCats->renderFromString($urlParams->catstring));
        }
        return;
    }

    /**
     * Returns JSON with list of categories at each level
     */
    public function categoriesJson()
    {
        $past = json_decode(stripslashes($this->request->getParam('pastSelection')));
        $catRepo = Repos::makeRepo('Categories');
        $displayCats = new Categories($catRepo);
        $this->setResponse($displayCats->renderChildrenJson($past));
        return;
    }
}
