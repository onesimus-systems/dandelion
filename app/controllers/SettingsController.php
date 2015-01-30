<?php
/**
 * Controller for the user settings page in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Utils\View;
use \Dandelion\Application;
use \League\Plates\Engine;

class SettingsController extends BaseController
{
	public function settings()
	{
        $userRights = new Rights($_SESSION['userInfo']['userid']);

        $templates = new Engine($this->app->paths['app'].'/templates');
        $templates->addFolder('layouts', $this->app->paths['app'].'/templates/layouts');
        $templates->registerFunction('getCssSheets', function($sheets = []) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadCssSheets'), $sheets);
        });

        $templates->registerFunction('loadJS', function($js) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadJS'), $js);
        });

        $templates->registerFunction('getThemeList', function() {
            return View::getThemeList();
        });

        $data = array(
            'appTitle' => $this->app->config['appTitle'],
            'tagline' => $this->app->config['tagline'],
            'appVersion' => Application::VERSION,
            'pageTitle' => 'Settings',
            'publicApiEnabled' => $this->app->config['publicApiEnabled'],
            'userRights' => $userRights
        );

        $templates->addData($data);

        try {
            echo $templates->render('settings');
        } catch (\Exception $e) {
            //$this->showTemplate('dashboard');
            // Show fall back error page
        }
	}
}
