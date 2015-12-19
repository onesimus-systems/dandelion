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
use Dandelion\API\ApiCommander;

class LogsAPI extends BaseModule
{
    /**
     *  Grab JSON array of logs
     *
     *  @return array
     */
    public function read($params)
    {
        if (!$this->authorized($this->requestUser, 'view_log')) {
            throw new ApiPermissionException();
        }

        $return = [];
        $logs = new Logs($this->repo, $this->ur);

        if ($params->logid) {
            $return = $logs->getLogInfo($params->logid);
        } else {
            $metadata = $this->offsetLimitCommon($params);
            $return = $logs->getLogList($metadata['limit'], $metadata['offset']);
            $metadata['resultCount'] = count($return);
            $return['metadata'] = $metadata;
        }

        return $return;
    }

    /**
     *  Add a new log
     */
    public function create($params)
    {
        if (!$this->authorized($this->requestUser, 'create_log')) {
            throw new ApiPermissionException();
        }

        $title = $params->title;
        $body = $params->body;
        $cat = rtrim($params->cat, ':');

        $logs = new Logs($this->repo);

        if ($logs->addLog($title, $body, $cat, $this->requestUser->get('id'))) {
            return 'Log created successfully';
        } else {
            throw new ApiException('Error creating log', ApiCommander::API_GENERAL_ERROR);
        }
    }

    /**
     * Save an edit to an existing log
     */
    public function edit($params)
    {
        $log = $this->read($params)[0];

        $canEdit = (
            $this->authorized($this->requestUser, 'admin') ||
            $this->authorized($this->requestUser, 'edit_any_log') ||
            ($this->authorized($this->requestUser, 'edit_log') && $log['user_id'] == $this->requestUser->get('id'))
        );

        if (!$canEdit) {
            throw new ApiPermissionException();
        }

        $lid = $params->logid;
        $title = $params->title;
        $body = $params->body;
        $cat = rtrim($this->cat, ':');

        $logs = new Logs($this->repo);

        if ($logs->editLog($lid, $title, $body, $cat)) {
            return "'{$title}' edited successfully";
        } else {
            throw new ApiException("Error saving log", ApiCommander::API_GENERAL_ERROR);
        }
    }

    /**
     * Search logs using provided query string
     * @return array
     */
    public function search($params)
    {
        if (!$this->authorized($this->requestUser, 'view_log')) {
            throw new ApiPermissionException();
        }

        $query = $params->query;

        $metadata = $this->offsetLimitCommon($params);

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
    private function offsetLimitCommon($params)
    {
        $userLimit = new UserSettings($this->makeRepo('UserSettings'));
        $limit = $userLimit->getSetting('logs_per_page', $this->requestUser->get('id'));
        $limit = $params->limit ?: $limit;
        $offset = $params->offset;
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
