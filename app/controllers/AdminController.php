<?php
/**
 * Controller for the administration page in Dandelion
 */
namespace Dandelion\Controllers;

use \Dandelion\Users;
use \Dandelion\Template;
use \Dandelion\Cheesto;
use \Dandelion\Groups;
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
            $permObj = new Groups(Repos::makeRepo('Groups'));
            $grouplist = $permObj->getGroupList();
            $grouplist2 = [];

            foreach ($grouplist as $group) {
                $grouplist2[$group['id']] = ['name' => $group['name'], 'users' => []];
                $usersInGroup = $permObj->usersInGroup($group['id']);

                foreach ($usersInGroup as $value) {
                    array_push($grouplist2[$group['id']]['users'], $value['username']);
                }
            }
        }

        $template = new Template($this->app);
        $template->addData([
            'userRights' => $this->rights,
            'userlist' => $userlist,
            'grouplist' => $grouplist2,
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

        $user = new Users(Repos::makeRepo('Users'));
        $groups = new Groups(Repos::makeRepo('Groups'));
        $cheesto = new Cheesto(Repos::makeRepo('Cheesto'));

        $template = new Template($this->app);
        $template->addData([
            'user' => $user->getUser($uid),
            'cheesto' => $cheesto->getUserStatus($uid),
            'grouplist' => $groups->getGroupList(),
            'statuslist' => $this->app->config['cheesto']['statusOptions']
        ]);
        $template->addFolder('admin', $this->app->paths['app'].'/templates/admin');
        $template->render('admin::edituser', 'User Management');
    }

    public function editGroup($gid = null)
    {
        if (!$gid) {
            View::redirect('adminSettings');
        }

        $this->loadRights();
        // Users without the proper permissions are redirected to the dashboard
        if (!$this->rights->authorized('editgroup')) {
            View::redirect('dashboard');
        }

        $permObj = new Groups(Repos::makeRepo('Groups'));

        $template = new Template($this->app);
        $group = $permObj->getGroupList($gid);
        $template->addData([
            'group' => $group,
            'usersInGroup' => $permObj->usersInGroup($group['id'])
        ]);
        $template->addFolder('admin', $this->app->paths['app'].'/templates/admin');
        $template->render('admin::editgroup', 'Group Management');
    }
}
