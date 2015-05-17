<?php
/**
 * Controller for the main dashboard in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Template;

class DashboardController extends BaseController
{
	public function dashboard()
	{
        $this->loadRights();

        $showCheesto = $this->rights->authorized('viewcheesto');
        $showLog = $this->rights->authorized('viewlog');
        $showCreateButton = $this->rights->authorized('createlog');

        $template = new Template($this->app);

        $template->addData([
            'showCheesto' => $showCheesto,
            'showLog' => $showLog,
            'createButton' => $showCreateButton ? '<button type="button" class="button" id="create-log-button">Create New</button>' : ''
        ]);

        $template->render('dashboard');
	}
}
