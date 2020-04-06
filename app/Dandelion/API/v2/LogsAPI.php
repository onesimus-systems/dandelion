<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\v2;

use Dandelion\Logs;
use Dandelion\LogSearch;
use Dandelion\UserSettings;
use Dandelion\Exception\ApiException;
use Dandelion\Controllers\ApiController;
use Dandelion\Exception\ApiPermissionException;
use Dandelion\API\ApiCommander;
use Dandelion\API\BaseModule;

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

        if ($params->id) {
            $return = $logs->getLogInfo($params->id);
        } else {
            $metadata = $this->offsetLimitCommon($params);
            $return['logs'] = $logs->getLogList($metadata['limit'], $metadata['offset']);
            $metadata['result_count'] = count($return['logs']);
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
        $cat = rtrim($params->category, ':');

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

        $lid = $params->id;
        $title = $params->title ?: null;
        $body = $params->body ?: null;
        $cat = $this->cat ? rtrim($this->cat, ':') : null;

        $logs = new Logs($this->repo);

        if (!$logs->editLog($lid, $title, $body, $cat)) {
            throw new ApiException("Error saving log", ApiCommander::API_GENERAL_ERROR);
        }

        return "'{$title}' edited successfully";
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

        $query = $params->q;

        $metadata = $this->offsetLimitCommon($params, false);

        $logs = new LogSearch($this->repo);
        $search = $logs->search($query, $metadata['limit'], $metadata['offset']);

        $result['logs'] = $search['logs'];
        $metadata['result_count'] = count($search['logs']);
        $metadata['log_size'] = $logs->search($query, -1, 0)['logs'][0];

        $result['metadata'] = $metadata;
        $result['query_data'] = $search['query_data'];
        return $result;
    }

    /**
     * Processes and compiles the offset, limit, and logsize used for paging
     * @return array Metadata used in the query and returned to client
     */
    private function offsetLimitCommon($params, $getSize = true)
    {
        $limit = $params->limit ?? $this->getUserSettingLogsPerPage();
        $offset = $params->offset ?: 0;
        $offset = $offset < 0 ? 0 : $offset;

        $logSize = $getSize ? $this->repo->numOfLogs() : 0;

        if ($offset > $logSize && $getSize) {
            $offset = $offset - $limit;
        }

        return [
            'offset' => $offset,
            'limit'  => (int) $limit,
            'log_size' => $logSize
        ];
    }

    private function getUserSettingLogsPerPage()
    {
        $userLimit = new UserSettings($this->makeRepo('UserSettings'));
        return (int) $userLimit->getSetting('logs_per_page', $this->requestUser->get('id'));
    }
}
