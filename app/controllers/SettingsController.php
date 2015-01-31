<?php
/**
 * Controller for the user settings page in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Template;
use \Dandelion\Utils\View;

class SettingsController extends BaseController
{
	public function settings()
	{
        $dbtype = ucfirst($this->app->config['db']['type']);
        $repo = "\\Dandelion\\Repos\\{$dbtype}\\RightsRepo";
        $rightsRepo = new $repo();
        $userRights = new Rights($_SESSION['userInfo']['userid'], $rightsRepo);

        $template = new Template($this->app);

        $template->registerFunction('getThemeList', function() {
            return View::getThemeList();
        });

        $template->addData([
            'publicApiEnabled' => $this->app->config['publicApiEnabled'],
            'userRights' => $userRights
        ]);

        $template->render('settings', 'User Settings');
	}
}
