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
        $this->loadRights();

        $template = new Template($this->app);

        $key = '';
        if ($this->app->config['publicApiEnabled']) {
            $keyManager = new KeyManager(Repos::makeRepo('KeyManager'));
            $key = $keyManager->getKey($_SESSION['userInfo']['userid']);
        }

        $template->addData([
            'publicApiEnabled' => $this->app->config['publicApiEnabled'],
            'apiKey' => $key,
            'themeInfo' => View::getThemeListArray()
        ]);

        $template->render('settings', 'User Settings');
	}
}
