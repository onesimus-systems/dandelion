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
        $template = new Template($this->app);
        $template->render('404notfound');
    }
}
