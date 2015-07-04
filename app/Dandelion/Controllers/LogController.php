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

class LogController extends BaseController
{
    public function render($logid)
    {
        $this->loadRights();

        if (!$logid || !$this->rights->authorized('viewlog')) {
            View::redirect('dashboard');
        }

        $newOldLink = '';
        $addCommentButton = '';
        $logs = new Logs(Repos::makeRepo('Logs'));
        $log = $logs->getLogInfo($logid);

        if (!$log) {
            $log = [
                'title' => 'Not Found',
                'body' => 'Log data was not found',
                'category' => '',
                'date_created' => date('Y-m-d'),
                'author' => 'Dandelion',
                'id' => $logid,
                'is_edited' => '',
                'time_created' => date('H:i:s'),
                'editButton' => ''
            ];
        } else {
            $log = $log[0];

            $commentOrder = $this->app->request->getParam('c_ord', 'new');
            $comments = $logs->getLogComments($logid, $commentOrder);

            if ($comments) {
                if ($commentOrder == 'old') {
                    $newOldLink = '<a href="?c_ord=new">Newest First</a>';
                } else {
                    $newOldLink = '<a href="?c_ord=old">Oldest First</a>';
                }
            }

            if ($this->rights->authorized('addcomment')) {
                $addCommentButton = '<button type="button" id="add-comment-btn">Add Comment</button>';
            }
        }

        $canEdit = ($this->rights->isAdmin() || $log['user_id'] == $this->rights->userid);

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
            'editButton' => $canEdit ? '<button type="button">Edit</button>' : '',
            'comments' => $comments,
            'newOldLink' => $newOldLink,
            'addCommentButton' => $addCommentButton
        ]);

        $this->setResponse($template->render('viewsinglelog', 'Log'));
    }
}
