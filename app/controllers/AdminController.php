<?php
/**
 * Controller for the administration page in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Users;
use \Dandelion\Template;
use \Dandelion\Permissions;
use \Dandelion\Utils\Repos;
use \Dandelion\Utils\View;

class AdminController extends BaseController
{
	public function admin($page = 'admin', $title = 'Administration')
	{
        $this->loadRights();
        $userlist = [];

        if ($this->rights->authorized(array('createuser', 'edituser', 'deleteuser'))) {
            $userObj = new Users(Repos::makeRepo('Users'));
            $userlist = $userObj->getUserList();
        }

        $template = new Template($this->app);
        $template->addData([
            'userRights' => $this->rights,
            'userlist' => $userlist
        ]);
        $template->render($page, $title);
	}

    public function editUser($uid = null)
    {
        if (!$uid) {
            View::redirect('dashboard');
        }

        $this->loadRights();
        // Users without the proper permissions are redirected to the dashboard
        if (!$this->rights->authorized(array('createuser', 'edituser', 'deleteuser'))) {
            View::redirect('dashboard');
        }

        $user = new Users(Repos::makeRepo('Users'), $uid, true);
        $groups = new Permissions(Repos::makeRepo('Rights'));

        $template = new Template($this->app);
        $template->addData([
            'userRights' => $this->rights,
            'user' => $user,
            'grouplist' => $groups->getGroupList(),
            'statuslist' => $this->app->config['cheesto']['statusOptions']
        ]);
        $template->render('edituser', 'User Management');
    }
}
