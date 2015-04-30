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
	public function admin()
	{
        $this->loadRights();
        $userlist = [];
        $grouplist = [];

        if ($this->rights->authorized('edituser', 'deleteuser')) {
            $userObj = new Users(Repos::makeRepo('Users'));
            $userlist = $userObj->getUserList();
        }

        if ($this->rights->authorized('creategroup', 'editgroup', 'deletegroup')) {
            $permObj = new Permissions(Repos::makeRepo('Rights'));
            $grouplist = $permObj->getGroupList();

            foreach ($grouplist as $key => $group) {
                $grouplist[$key]['users'] = [];
                $usersInGroup = $permObj->usersInGroup($group['role']);
                foreach ($usersInGroup as $value) {
                    array_push($grouplist[$key]['users'], $value['username']);
                }
            }
        }

        $template = new Template($this->app);
        $template->addData([
            'userRights' => $this->rights,
            'userlist' => $userlist,
            'grouplist' => $grouplist,
            'catList' => $this->rights->authorized('createcat', 'editcat', 'deletecat')
        ]);
        $template->addFolder('admin', $this->app->paths['app'].'/templates/admin');
        $template->render('admin::admin', 'Administration');
	}

    public function editUser($uid = null)
    {
        if (!$uid) {
            View::redirect('adminSettings');
        }

        $this->loadRights();
        // Users without the proper permissions are redirected to the dashboard
        if (!$this->rights->authorized('edituser', 'deleteuser')) {
            View::redirect('dashboard');
        }

        $user = new Users(Repos::makeRepo('Users'), $uid, true);
        $groups = new Permissions(Repos::makeRepo('Rights'));

        $template = new Template($this->app);
        $template->addData([
            'user' => $user,
            'grouplist' => $groups->getGroupList(),
            'statuslist' => $this->app->config['cheesto']['statusOptions']
        ]);
        $template->addFolder('admin', $this->app->paths['app'].'/templates/admin');
        $template->render('admin::edituser', 'User Management');
    }

    public function editGroup($gname = null)
    {
        if (!$gname) {
            View::redirect('adminSettings');
        }

        $this->loadRights();
        // Users without the proper permissions are redirected to the dashboard
        if (!$this->rights->authorized('editgroup')) {
            View::redirect('dashboard');
        }

        $permObj = new Permissions(Repos::makeRepo('Rights'));

        $template = new Template($this->app);
        $group = $permObj->getGroupList($gname);
        $template->addData([
            'group' => $group,
            'usersInGroup' => $permObj->usersInGroup($group['role'])
        ]);
        $template->addFolder('admin', $this->app->paths['app'].'/templates/admin');
        $template->render('admin::editgroup', 'Group Management');
    }
}
