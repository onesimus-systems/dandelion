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

class DashboardController extends BaseController
{
	public function dashboard()
	{
        $showCheesto = $this->authorized($this->sessionUser, 'view_cheesto');
        $showLog = $this->authorized($this->sessionUser, 'view_log');
        $showCreateButton = $this->authorized($this->sessionUser, 'create_log');

        $template = new Template($this->app);

        $template->addData([
            'showCheesto' => $showCheesto,
            'showLog' => $showLog,
            'showCreateButton' => $showCreateButton
        ]);

        $this->setResponse($template->render('dashboard', 'Dashboard'));
	}
}
