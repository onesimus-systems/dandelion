<?php
/**
 * Controller for the main dashboard in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Application;
use \League\Plates\Engine;

class DashboardController extends BaseController
{
	public function dashboard()
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

        $data = array(
            'appTitle' => $this->app->config['appTitle'],
            'tagline' => $this->app->config['tagline'],
            'appVersion' => Application::VERSION,
            'pageTitle' => 'Dashboard',
            'cheestoEnabled' => $this->app->config['cheestoEnabled'],
            'userRights' => $userRights
        );

        $templates->addData($data);

        try {
            echo $templates->render('dashboard');
        } catch (\Exception $e) {
            //$this->showTemplate('dashboard');
            // Show fall back error page
        }
	}
}
