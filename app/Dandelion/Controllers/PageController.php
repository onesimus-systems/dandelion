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

use \Dandelion\Template;

class PageController extends BaseController
{
    public function render($page)
    {
        $template = new Template($this->app);
        $template->render($page);
    }
}
