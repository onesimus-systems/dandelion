<?php
/**
 * Controller for the administration page in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Application;
use \League\Plates\Engine;

class AdminController extends BaseController
{
	public function admin($title = 'admin')
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
            'pageTitle' => ucfirst($title),
            'userRights' => $userRights
        );

        $templates->addData($data);

        try {
            echo $templates->render($title);
        } catch (\Exception $e) {
            //$this->showTemplate('dashboard');
            // Show fall back error page
        }
	}

    public function editUsers()
    {
        $this->admin('editusers');
    }

    public function editGroups()
    {
        $this->admin('editgroups');
    }

    public function editCategories()
    {
        $this->admin('categories');
    }
}
