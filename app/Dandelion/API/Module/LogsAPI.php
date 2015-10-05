<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\Module;

use Dandelion\Logs;
use Dandelion\LogSearch;
use Dandelion\UserSettings;
use Dandelion\Exception\ApiException;
use Dandelion\Controllers\ApiController;
use Dandelion\Exception\ApiPermissionException;

class LogsAPI extends BaseModule
{
    /**
     *  Grab JSON array of logs
     *
     *  @return array
     */
    public function read()
    {
        if (!$this->authorized($this->requestUser, 'view_log')) {
            throw new ApiPermissionException();
        }

        $return = [];
        $logs = new Logs($this->repo, $this->ur);

        if ($this->request->getParam('logid')) {
            $return = $logs->getLogInfo($this->request->getParam('logid'));
        } else {
            $metadata = $this->offsetLimitCommon();
            $return = $logs->getLogList($metadata['limit'], $metadata['offset']);
            $metadata['resultCount'] = count($return);
            $return['metadata'] = $metadata;
        }

        return $return;
    }

    /**
     *  Add a new log
     */
    public function create()
    {
        if (!$this->authorized($this->requestUser, 'create_log')) {
            throw new ApiPermissionException();
        }

        $title = $this->request->postParam('title');
        $body = $this->request->postParam('body');
        $cat = rtrim($this->request->postParam('cat'), ':');

        $logs = new Logs($this->repo);

        if ($logs->addLog($title, $body, $cat, $this->requestUser->get('id'))) {
            return 'Log created successfully';
        } else {
            throw new ApiException('Error creating log', 5);
        }
    }

    /**
     * Save an edit to an existing log
     */
    public function edit()
    {
        $lid = $this->request->postParam('logid');
        $log = $this->read()[0];

        $canEdit = (
            $this->authorized($this->requestUser, 'admin') ||
            ($this->authorized($this->requestUser, 'edit_log') && $log['user_id'] == $this->requestUser->get('id'))
        );

        if (!$canEdit) {
            throw new ApiPermissionException();
        }

        $title = $this->request->postParam('title');
        $body = $this->request->postParam('body');
        $cat = rtrim($this->request->postParam('cat'), ':');

        $logs = new Logs($this->repo);

        if ($logs->editLog($lid, $title, $body, $cat)) {
            return "'{$title}' edited successfully";
        } else {
            throw new ApiException("Error saving log", 5);
        }
    }

    /**
     * Search logs using provided query string
     * @return array
     */
    public function search()
    {
        if (!$this->authorized($this->requestUser, 'view_log')) {
            throw new ApiPermissionException();
        }

        $query = $this->request->getParam('query');

        $metadata = $this->offsetLimitCommon();

        $logs = new LogSearch($this->repo);
        // It would appear the +1 extra log, and the -1 count would cancel out...
        // Well, they don't. So don't touch them
        $return = $logs->search($query, $metadata['limit']+1, $metadata['offset']);
        $metadata['resultCount'] = count($return)-1;
        unset($return[$metadata['limit']]);

        $return['metadata'] = $metadata;
        return $return;
    }

    /**
     * Processes and compiles the offset, limit, and logsize used for paging
     * @return array Metadata used in the query and returned to client
     */
    private function offsetLimitCommon()
    {
        $userLimit = new UserSettings($this->makeRepo('UserSettings'));
        $limit = $userLimit->getSetting('logs_per_page', $this->requestUser->get('id'));
        $limit = (int) $this->request->getParam('limit', $limit);
        $offset = (int) $this->request->getParam('offset', 0);
        $offset = $offset < 0 ? 0 : $offset;

        $logSize = $this->repo->numOfLogs();

        if ($offset > $logSize) {
            $offset = $offset - $limit;
        }

        return [
            'offset' => $offset,
            'limit'  => $limit,
            'logSize' => $logSize
        ];
    }
}
