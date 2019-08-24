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

use Dandelion\Logs;
use Dandelion\Template;
use Dandelion\Utils\View;
use Dandelion\Utils\Repos;
use Dandelion\Categories;
use Dandelion\Utils\Configuration as Config;
use Dandelion\Session\SessionManager as Session;

class LogController extends BaseController
{
    public function show($logid)
    {
        if (!$logid || !$this->authorized($this->sessionUser, 'view_log')) {
            View::redirect('dashboard');
            return;
        }

        $commentOrderLink = '';
        $addCommentButton = '';
        $logs = new Logs(Repos::makeRepo('Logs'));
        $log = $this->getLog($logid, $logs);

        if ($log['_found']) {
            // Default comment order is newest first
            $commentOrder = $this->app->request->getParam('c_ord', 'new');
            $comments = $logs->getLogComments($logid, $commentOrder);

            if ($comments) {
                if ($commentOrder == 'old') {
                    $commentOrderLink = '<a href="?c_ord=new">Newest First</a>';
                } else {
                    $commentOrderLink = '<a href="?c_ord=old">Oldest First</a>';
                }
            }

            if ($this->authorized($this->sessionUser, 'add_comment')) {
                $addCommentButton = '<button type="button" id="add-comment-btn">Add Comment</button>';
            }
        }

        $canEdit = (
            $this->authorized($this->sessionUser, 'admin') ||
            $this->authorized($this->sessionUser, 'edit_any_log') ||
            ($this->authorized($this->sessionUser, 'edit_log') && $log['user_id'] == $this->sessionUser->get('id'))
        );

        $template = new Template($this->app);

        $template->addData([
            'title' => $log['title'],
            'body' => $log['body'],
            'category' => $log['category'],
            'date_created' => $log['date_created'],
            'author' => $log['fullname'],
            'id' => $logid,
            'is_edited' => $log['is_edited'] ? 'Yes' : 'No',
            'time_created' => $log['time_created'],
            'editButton' => $canEdit ? '<button type="button" id="edit-log-btn">Edit</button>' : '',
            'dupButton' => $this->authorized($this->sessionUser, 'create_log') ? "<a href=\"/log/duplicate?template={$logid}\" class=\"button\">Duplicate</a>" : '',
            'comments' => $comments,
            'newOldLink' => $commentOrderLink,
            'addCommentButton' => $addCommentButton
        ]);

        $this->setResponse($template->render('viewsinglelog', 'Log'));
    }

    private function getLog($id, Logs $logs)
    {
        $log = $logs->getLogInfo($id);

        if (!$log) {
            return [
                'title' => 'Not Found',
                'body' => 'Log data was not found',
                'category' => '',
                'date_created' => date('Y-m-d'),
                'author' => 'Dandelion',
                'id' => $id,
                'is_edited' => '',
                'time_created' => date('H:i:s'),
                'editButton' => '',
                '_found' => false,
            ];
        }

        $log[0]['_found'] = true;
        return $log[0];
    }

    public function edit($logid)
    {
        // Check for a logid
        if (!$logid) {
            View::redirect('dashboard');
            return;
        }

        $logs = new Logs(Repos::makeRepo('Logs'));
        $log = $this->getLog($logid, $logs);

        // Determine if the user has the correct rights to edit the log
        $canEdit = (
            $this->authorized($this->sessionUser, 'admin') ||
            $this->authorized($this->sessionUser, 'edit_any_log') ||
            ($this->authorized($this->sessionUser, 'edit_log')
                && $log['user_id'] == $this->sessionUser->get('id'))
        );

        // If not, boot them out
        if (!$canEdit) {
            View::redirect('dashboard');
            return;
        }

        $displayCats = new Categories(Repos::makeRepo('Categories'));
        $cats = $displayCats->renderFromString($log['category']);

        $template = new Template($this->app);

        $template->addData([
            'title' => $log['title'],
            'body' => $log['body'],
            'category' => $cats,
            'date_created' => $log['date_created'],
            'author' => $log['fullname'],
            'id' => $logid,
            'is_edited' => $log['is_edited'] ? 'Yes' : 'No',
            'time_created' => $log['time_created'],
            'last_error' => Session::get('last_error', '')
        ]);
        Session::remove('last_error');

        $this->setResponse($template->render('editlog', 'Edit Log '.$logid));
    }

    public function save()
    {
        $postParams = $this->app->request->postParam();

        $logid = $postParams['log-id'];
        if ($logid == 0) {
            $this->saveNew($postParams);
        } else {
            $this->saveExisting($postParams);
        }
    }

    private function saveNew(array $postParams)
    {
        if (!$this->authorized($this->sessionUser, 'create_log')) {
            View::redirect('dashboard');
            return;
        }

        $title = $postParams['title'];
        $body = $postParams['body'];
        $cat = rtrim($postParams['catstring'], ':');

        $logs = new Logs(Repos::makeRepo('Logs'));
        $newId = $logs->addLog($title, $body, $cat, Session::get('userInfo')['id']);

        if ($newId != 0) {
            $this->app->response->redirect(Config::get('hostname').'/log/'.$newId);
        } else {
            Session::set('last_error', 'Failed to save new log');
            $this->app->response->redirect(Config::get('hostname').'/log/new');
        }
    }

    private function saveExisting(array $postParams)
    {
        $logid = $postParams['log-id'];
        $logs = new Logs(Repos::makeRepo('Logs'));
        $log = $this->getLog($logid, $logs);

        $canEdit = (
            $this->authorized($this->sessionUser, 'admin') ||
            $this->authorized($this->sessionUser, 'edit_any_log') ||
            ($this->authorized($this->sessionUser, 'edit_log') && $log['user_id'] == $this->sessionUser->get('id'))
        );

        if (!$canEdit) {
            View::redirect('dashboard');
            return;
        }

        $title = $postParams['title'];
        $body = $postParams['body'];
        $cat = rtrim($postParams['catstring'], ':');

        if ($logs->editLog($logid, $title, $body, $cat)) {
            $this->app->response->redirect(Config::get('hostname').'/log/'.$logid);
        } else {
            Session::set('last_error', 'Failed to save log');
            $this->app->response->redirect(Config::get('hostname').'/log/edit/'.$logid);
        }
    }

    public function create()
    {
        $template = new Template($this->app);
        $template->addData([
            'last_error' => Session::get('last_error', '')
        ]);
        Session::remove('last_error');
        $this->setResponse($template->render('addlog', 'Create Log'));
    }

    public function duplicate()
    {
        $logid = $this->request->getParam('template');

        // Check for a logid
        if (!$logid) {
            View::redirect('dashboard');
            return;
        }

        $logs = new Logs(Repos::makeRepo('Logs'));
        $log = $this->getLog($logid, $logs);

        if (!$this->authorized($this->sessionUser, 'create_log')) {
            View::redirect('dashboard');
            return;
        }

        $displayCats = new Categories(Repos::makeRepo('Categories'));
        $cats = $displayCats->renderFromString($log['category']);

        $template = new Template($this->app);

        $template->addData([
            'title' => $log['title'],
            'body' => $log['body'],
            'category' => $cats,
            'last_error' => Session::get('last_error', ''),
        ]);
        Session::remove('last_error');

        $this->setResponse($template->render('duplog', 'Create Log'));
    }
}
