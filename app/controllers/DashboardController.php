<?php
/**
 * Controller for the main dashboard in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Template;
use \Dandelion\Utils\Repos;

class DashboardController extends BaseController
{
	public function dashboard()
	{
        $rightsRepo = Repos::makeRepo($this->app->config['db']['type'], 'Rights');
        $userRights = new Rights($_SESSION['userInfo']['userid'], $rightsRepo);

        $showCheesto = ($this->app->config['cheestoEnabled'] && $userRights->authorized('viewcheesto'));
        $showLog = $userRights->authorized('viewlog');
        $showCreateButton = $userRights->authorized('createlog');

        $template = new Template($this->app);

        $template->addData([
            'showCheesto' => $showCheesto,
            'showLog' => $showLog,
            'showCreateButton' => $showCreateButton
        ]);

        $template->render('dashboard');
	}
}
