<?php
/**
 * Controller for the main dashboard in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Template;

class DashboardController extends BaseController
{
	public function dashboard()
	{
        $userRights = new Rights($_SESSION['userInfo']['userid']);
        $template = new Template($this->app);

        $template->addData([
            'cheestoEnabled' => $this->app->config['cheestoEnabled'],
            'userRights' => $userRights
        ]);

        $template->render('dashboard');
	}
}
