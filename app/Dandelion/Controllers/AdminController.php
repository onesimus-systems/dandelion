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

use Dandelion\Factory\UserFactory;

class AdminController extends BaseController
{
    public function admin()
    {
        $this->loadRights();
        $userlist = [];
        $grouplist = [];
        $updateArray = [];

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

        if (Config::get('checkForUpdates')) {
            $latest = file('http://onesimussystems.com/dandelion/versioncheck', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
            $latest = json_decode($latest[0], true);
            if (version_compare($latest['version'], Application::VERSION, '>')) {
                $updateArray['current'] = Application::VERSION;
                $updateArray['latest'] = $latest['version'];
                $updateArray['url'] = $latest['url'];
            }
        }

        $template = new Template($this->app);
        $template->addData([
            'userRights' => $this->rights,
            'userlist' => $userlist,
            'grouplist' => $grouplist2,
            'catList' => $this->rights->authorized('createcat', 'editcat', 'deletecat'),
            'showUpdateSection' => Config::get('checkForUpdates'),
            'updates' => $updateArray
        ]);
        $template->addFolder('admin', $this->app->paths['app'].'/templates/admin');
        $this->setResponse($template->render('admin::admin', 'Administration'));
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
        $this->setResponse($template->render('admin::editgroup', 'Group Management'));
    }
}
