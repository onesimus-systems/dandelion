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
            if ($this->ur->isAdmin() || $value['usercreated'] == $this->ur->userid) {
                $getLogs[$key]['canEdit'] = true;
            } else {
                $getLogs[$key]['canEdit'] = false;
            }
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
        if (empty($title) || empty($body) || empty($cat) || $cat == 'Select:') {
            return 'Log entries require a title, category, and body.';
        }

        $datetime = getdate();
        $date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
        $time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];

        if ($this->repo->addLog($uid, $title, $body, $cat, $date, $time)) {
            return 'Log entry created successfully.';
        } else {
            return 'An error occured saving the log entry.';
        }
    }

    /**
     * Update log in database
     *
     *
     *
     * @return string Confirmation message or error message
     */
    public function editLog($lid, $title, $body)
    {
        if (empty($body) || empty($title) || empty($lid)) {
            return 'Log entries require a title, category, and body.';
        }

        if ($this->repo->updateLog($lid, $title, $body)) {
            return "\"{$title}\" edited successfully.";
        } else {
            return 'There was an error saving the log.';
        }
    }

    /**
     * Filter logs by category
     */
    public function filter($filter)
    {
        return $this->repo->getLogsByFilter($filter);
    }

    /**
     * Search logs by title and content
     *
     * @param string $kw - Keywords
     * @param string $date - Date of creation
     *
     * @return
     */
    public function search($kw = '', $date = '')
    {
        return $this->repo->getLogsBySearch($kw, $date);
    }
}
