<?php
/**
 * Controller for the user settings page in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Template;
use \Dandelion\Utils\View;
use \Dandelion\Utils\Repos;
use \Dandelion\KeyManager;

class SettingsController extends BaseController
{
	public function settings()
	{
        $rightsRepo = Repos::makeRepo($this->app->config['db']['type'], 'Rights');
        $userRights = new Rights($_SESSION['userInfo']['userid'], $rightsRepo);

        $template = new Template($this->app);

        $template->registerFunction('getThemeList', function() {
            return View::getThemeList();
        });

        $key = '';
        if ($this->app->config['publicApiEnabled']) {
            $keyRepo = Repos::makeRepo($this->app->config['db']['type'], 'KeyManager');
            $keyManager = new KeyManager($keyRepo);
            $key = $keyManager->getKey($_SESSION['userInfo']['userid']);
        }

        $template->addData([
            'publicApiEnabled' => $this->app->config['publicApiEnabled'],
            'apiKey' => $key,
            'userRights' => $userRights
        ]);

        $template->render('settings', 'User Settings');
	}
}
