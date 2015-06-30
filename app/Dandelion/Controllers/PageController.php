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
        $this->setResponse($template->render($page));
    }

    public function renderErrorPage($message = '')
    {
        $this->app->response->setStatus(500);

        if (!$message) {
            $message = "But don't worry, it has been logged and the repair monkies are going to work.";
        }

        $errorPage = new Template($this->app);
        $errorPage->addData(['message' => $message]);
        $this->setResponse($errorPage->render('error', 'An error has occured'));
    }
}
