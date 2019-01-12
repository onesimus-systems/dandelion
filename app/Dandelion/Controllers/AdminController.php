<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Controllers;

use Dandelion\Users;
use Dandelion\Template;
use Dandelion\Cheesto;
use Dandelion\Groups;
use Dandelion\Application;
use Dandelion\Utils\Repos;
use Dandelion\Utils\View;
use Dandelion\Utils\Configuration as Config;
use Dandelion\Factories\UserFactory;

class AdminController extends BaseController
{
    public function admin()
    {
        $userlist = [];
        $grouplist = [];
        $grouplist2 = [];

        if ($this->authorized($this->sessionUser, 'manage_current_users')) {
            $userObj = new Users(Repos::makeRepo('Users'));
            $userlist = $userObj->getUserList();
        }

        if ($this->authorized($this->sessionUser, 'manage_groups')) {
            $permObj = new Groups(Repos::makeRepo('Groups'));
            $grouplist = $permObj->getGroupList();

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
            'catList' => $this->authorized($this->sessionUser, 'manage_categories'),
        ]);
        $template->addFolder('admin', $this->app->paths['app'].'/templates/admin');
        $this->setResponse($template->render('admin::admin', 'Administration'));
    }

    public function editUser($uid = null)
    {
        if (!$uid) {
            View::redirect('adminSettings');
        }

        // Users without the proper permissions are redirected to the dashboard
        if (!$this->authorized($this->sessionUser, 'manage_current_users')) {
            View::redirect('dashboard');
        }

        $user = (new UserFactory())->get($uid);
        $groups = new Groups(Repos::makeRepo('Groups'));
        $cheesto = new Cheesto(Repos::makeRepo('Cheesto'));

        $template = new Template($this->app);
        $template->addData([
            'user' => $user,
            'cheesto' => $cheesto->getUserStatus($uid),
            'grouplist' => $groups->getGroupList(),
            'statuslist' => Config::get('cheesto', ['statusOptions' => []])['statusOptions']
        ]);
        $template->addFolder('admin', $this->app->paths['app'].'/templates/admin');
        $this->setResponse($template->render('admin::edituser', 'User Management'));
    }

    public function editGroup($gid = null)
    {
        if (!$gid) {
            View::redirect('adminSettings');
        }

        // Users without the proper permissions are redirected to the dashboard
        if (!$this->authorized($this->sessionUser, 'manage_current_groups')) {
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
        $this->setResponse($template->render('admin::editgroup', 'Group Management'));
    }
}
