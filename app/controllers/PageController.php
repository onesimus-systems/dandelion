<?php
/**
 * Controller for simple pages such as tutorial and about
 */
namespace Dandelion\Controllers;

use \Dandelion\Template;

class PageController extends BaseController
{
    public function render($page)
    {
        $template = new Template($this->app);
        $template->render($page);
    }
}
