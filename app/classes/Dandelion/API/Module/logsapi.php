<?php
/**
 *  Logs API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Logs;
use \Dandelion\LogSearch;
use \Dandelion\UserSettings;
use \Dandelion\Controllers\ApiController;

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
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $metadata = $this->offsetLimitCommon();

        $logs = new Logs($this->repo, $this->ur);
        $return = $logs->getLogList($metadata['limit'], $metadata['offset']);
        $metadata['resultCount'] = count($return);
        $return['metadata'] = $metadata;
        return $return;
    }

    /**
     *  Get data for a single log
     */
    public function readOne()
    {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $logs = new Logs($this->repo);
        return $logs->getLogInfo($this->up->logid);
    }

    /**
     *  Add a new log
     */
    public function create()
    {
        if (!$this->ur->authorized('createlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $title = $this->up->title;
        $body = $this->up->body;
        $cat = rtrim($this->up->cat, ':');

        $logs = new Logs($this->repo);
        return $logs->addLog($title, $body, $cat, USER_ID);
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
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
            }
        }

        $title = $this->up->title;
        $body = $this->up->body;
        $cat = rtrim($this->up->cat, ':');

        $logs = new Logs($this->repo);
        return $logs->editLog($lid, $title, $body, $cat);
    }

    /**
     * Search logs using provided query string
     * @return array
     */
    public function search()
    {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
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
        $limit = $userLimit->getSetting('showlimit', USER_ID);
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
