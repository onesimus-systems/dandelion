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

use Dandelion\Template;
use Dandelion\Exception\Template404Exception;

class PageController extends BaseController
{
    public function render($page)
    {
        $template = new Template($this->app);
        $template->render($page);
    }

    public function renderErrorPage($message = '')
    {
        header("HTTP/1.1 500 Internal Server Error");

        if (!$message) {
            $message = "But don't worry, it has been logged and the repair monkies are going to work.";
        }

        $errorPage = new Template($this->app);
        $errorPage->addData(['message' => $message]);
        $errorPage->render('error');
    }
}
