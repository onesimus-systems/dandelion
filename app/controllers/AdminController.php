<?php
/**
 * Controller for the administration page in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Template;
use \Dandelion\Utils\Repos;

class AdminController extends BaseController
{
	public function admin($page = 'admin', $title = 'Administration')
	{
        $rightsRepo = Repos::makeRepo($this->app->config['db']['type'], 'Rights');
        $userRights = new Rights($_SESSION['userInfo']['userid'], $rightsRepo);

        $template = new Template($this->app);
        $template->addData(['userRights' => $userRights]);
        $template->render($page, $title);
	}

    public function editUsers()
    {
        $this->admin('editusers', 'User Management');
    }

    public function editGroups()
    {
        $this->admin('editgroups', 'Group Management');
    }

    public function editCategories()
    {
        $this->admin('categories', 'Category Management');
    }
}
