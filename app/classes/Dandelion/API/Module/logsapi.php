<?php
/**
 *  Logs API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Logs;
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

        $userLimit = new UserSettings($this->makeRepo('UserSettings'));
        $limit = $userLimit->getSetting('showlimit', USER_ID);
        $limit = (int) $this->up->get('limit', $limit);
        $offset = (int) $this->up->get('offset', 0);
        $offset = $offset < 0 ? 0 : $offset;

        $logSize = $this->repo->numOfLogs();

        if ($offset > $logSize) {
            $offset = $offset - $limit;
        }

        $metaData = array(
            'offset' => $offset,
            'limit'  => $limit,
            'logSize' => $logSize
        );

        $logs = new Logs($this->repo, $this->ur);
        $return = $logs->getLogList($limit, $offset);
        $return['metadata'] = $metaData;
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
        // Eventually editing log categories will be a thing
        //$cat = $this->up->cat;

        //$cat = rtrim($cat, ':');

        $logs = new Logs($this->repo);
        return $logs->editLog($lid, $title, $body);
    }

    /**
     *  Filter logs by category
     */
    public function filter()
    {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $filter = $this->up->filter;
        $filter = rtrim($filter, ':');

        $logs = new Logs($this->repo);
        return $logs->filter($filter);
    }

    /**
     *  Search logs by title, content, and date
     */
    public function search()
    {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $kw = $this->up->kw;
        $date = $this->up->date;

        $logs = new Logs($this->repo);
        return $logs->search($kw, $date);
    }
}
