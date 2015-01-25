<?php
/**
 *  Logs API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Logs;
use \Dandelion\UserSettings;
use \Dandelion\Controllers\ApiController;

class logsAPI extends BaseModule
{
    public function __construct($db, $ur, $params)
    {
        parent::__construct($db, $ur, $params);
    }

    /**
     *  Grab JSON array of logs
     *
     *  @return JSON
     */
    public function read()
    {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $userLimit = new UserSettings($this->db);
        $limit = $userLimit->getSetting('showlimit', USER_ID);
        $limit = (int) $this->up->get('limit', $limit);
        $offset = (int) $this->up->get('offset', 0);
        $offset = $offset < 0 ? 0 : $offset;

        $logSize = (int) $this->db->numOfRows('log');

        if ($offset > $logSize) {
            $offset = $offset - $limit;
        }

        $metaData = array(
            'offset' => $offset,
            'limit'  => $limit,
            'logSize' => $logSize
        );

        $logs = new Logs($this->db, $this->ur);
        $return = json_decode($logs->getJSON($limit, $offset), true);
        $return['metadata'] = $metaData;
        return json_encode($return);
    }

    /**
     *  Get data for a single log
     */
    public function readOne()
    {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $logs = new Logs($this->db);
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

        $logs = new Logs($this->db);
        return json_encode($logs->addLog($title, $body, $cat, USER_ID));
    }

    /**
     * Save an edit to an existing log
     */
    public function edit()
    {
        $lid = $this->up->logid;

        if (!$this->ur->isAdmin()) {
            $log = json_decode(self::readOne($this->db, $this->ur), true);

            if (!$this->ur->authorized('editlog') || $log['usercreated'] != USER_ID) {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
            }
        }

        $title = $this->up->title;
        $body = $this->up->body;
        // Eventually editing log categories will be a thing
        //$cat = $this->up->cat;

        //$cat = rtrim($cat, ':');

        $logs = new Logs($this->db);
        return json_encode($logs->editLog($lid, $title, $body));
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

        $logs = new Logs($this->db);
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

        $logs = new Logs($this->db);
        return $logs->search($kw, $date);
    }
}
