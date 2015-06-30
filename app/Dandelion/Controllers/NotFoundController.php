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

class NotFoundController extends BaseController
{
    public function render()
    {
        $this->app->response->setStatus(404);
        $template = new Template($this->app);
        $this->setResponse($template->render('404notfound', 'Page Not Found'));
    }
}
