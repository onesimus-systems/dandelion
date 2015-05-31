<?php
/**
 *  Logs API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Logs;
use \Dandelion\LogSearch;
use \Dandelion\UserSettings;
use \Dandelion\Exception\ApiException;
use \Dandelion\Controllers\ApiController;
use \Dandelion\Exception\ApiPermissionException;

class LogsAPI extends BaseModule
{
    /**
     *  Grab JSON array of logs
     *
     *  @return array
     */
    public function read()
    {
        if (!$this->ur->authorized('viewlog')) {
            throw new ApiPermissionException();
        }

        $return = [];
        $logs = new Logs($this->repo, $this->ur);

        if ($this->up->logid) {
            $return = $logs->getLogInfo($this->up->logid);
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
        if (!$this->ur->authorized('createlog')) {
            throw new ApiPermissionException();
        }

        $title = $this->up->title;
        $body = $this->up->body;
        $cat = rtrim($this->up->cat, ':');

        $logs = new Logs($this->repo);

        if ($logs->addLog($title, $body, $cat, USER_ID)) {
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
        $lid = $this->up->logid;

        if (!$this->ur->isAdmin()) {
            $log = json_decode($this->readOne(), true);

            if (!$this->ur->authorized('editlog') || $log['usercreated'] != USER_ID) {
                throw new ApiPermissionException();
            }
        }

        $title = $this->up->title;
        $body = $this->up->body;
        $cat = rtrim($this->up->cat, ':');

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
        if (!$this->ur->authorized('viewlog')) {
            throw new ApiPermissionException();
        }

        $query = $this->up->query;

        $metadata = $this->offsetLimitCommon();

        $logs = new LogSearch($this->repo);
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
        $limit = $userLimit->getSetting('logs_per_page', USER_ID);
        $limit = (int) $this->up->get('limit', $limit);
        $offset = (int) $this->up->get('offset', 0);
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
