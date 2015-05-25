<?php
/**
 * User log management
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\LogsRepo;

class Logs
{
    public function __construct(LogsRepo $repo, Rights $ur = null)
    {
        $this->repo = $repo;
        $this->ur = $ur;
        return;
    }

    /**
     * Get log data from database
     *
     * @param int $logid Row id number for desired log
     *
     * @return array with log data
     */
    public function getLogInfo($logid)
    {
        $logid = isset($logid) ? $logid : '';

        return $this->repo->getLogInfo($logid);
    }

    /**
     * Get JSON of log list showing $limit number of logs
     *
     * @param int $limit - Number of logs to get
     * @param int $offset - Offset for pagination
     */
    public function getLogList($limit = 25, $offset = 0)
    {
        $getLogs = $this->repo->getLogList($offset, $limit);

        foreach ($getLogs as $key => $value) {
            $getLogs[$key]['canEdit'] = ($this->ur->isAdmin() || $value['user_id'] == $this->ur->userid);
        }
        return $getLogs;
    }

    /**
     * Create a new log in the database
     *
     * @param string $title
     * @param string $body
     * @param string $cat
     * @param int $uid - User ID
     *
     * @return string Confirmation message or error message
     */
    public function addLog($title, $body, $cat, $uid)
    {
        if (!$title || !$body || !$cat || strtolower($cat) == 'select') {
            return false;
        }

        $datetime = getdate();
        $date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
        $time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];

        return $this->repo->addLog($uid, $title, $body, $cat, $date, $time);
    }

    /**
     * Update log in database
     *
     *
     *
     * @return string Confirmation message or error message
     */
    public function editLog($lid, $title, $body, $cat)
    {
        if (empty($body) || empty($title) || empty($lid) || empty($cat)) {
            return false;
        }

        return $this->repo->updateLog($lid, $title, $body, $cat);
    }
}
