<?php
/**
 * MySQL repository for the Logs module
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class LogsRepo extends BaseMySqlRepo implements Interfaces\LogsRepo
{
    public function numOfLogs()
    {
        return (int) $this->database->numOfRows('log');
    }

    public function getLogInfo($lid)
    {
        $this->database->select()
                       ->from($this->prefix.'log')
                       ->where('logid = :lid');

        return $this->database->getFirst(['lid' => $lid]);
    }

    public function getLogList($offset, $limit)
    {
        $this->database->select('l.*, u.realname')
                       ->from($this->prefix.'log AS l
                            LEFT JOIN '.$this->prefix.'users AS u
                                ON l.usercreated = u.userid')
                       ->orderBy('l.logid', 'DESC')
                       ->limit(':pO,:lim');

        $params = [
            'pO' => ((int) $offset),
            'lim' => ((int) $limit)
        ];

        // When using an SQL LIMIT, the parameter MUST be an integer.
        // To accomplish this the PDO constant PARAM_INT is passed
        return $this->database->get($params, \PDO::PARAM_INT);
    }

    public function addLog($uid, $title, $body, $cat, $date, $time)
    {
        $this->database->insert()
                       ->into($this->prefix.'log', ['datec', 'timec', 'title', 'entry', 'usercreated', 'cat'])
                       ->values([':datec', ':timec', ':title', ':entry', ':usercreated', ':cat']);

        $params = [
            'datec' => $date,
            'timec' => $time,
            'title' => $title,
            'entry' => $body,
            'usercreated' => $uid,
            'cat' => $cat
        ];

        return $this->database->go($params);
    }

    public function updateLog($lid, $title, $body)
    {
        $this->database->update($this->prefix.'log')
                       ->set('title = :title, entry = :entry, edited = 1')
                       ->where('logid = :lid');
        $params = [
            'title' => $title,
            'entry' => $body,
            'lid' => $lid
        ];

        return $this->database->go($params);
    }

    public function getLogsByFilter($filter)
    {
        $this->database->select('l.*, u.realname')
                       ->from($this->prefix.'log AS l
                            LEFT JOIN '.$this->prefix.'users AS u
                                ON l.usercreated = u.userid')
                       ->where('cat LIKE :filter')
                       ->orderBy('logid', 'DESC');

        return $this->database->get(['filter' => "%{$filter}%"]);
    }

    public function getLogsBySearch($kw, $date)
    {
        $this->database->select('l.*, u.realname')
                       ->from($this->prefix.'log AS l LEFT JOIN '.$this->prefix.'users AS u ON l.usercreated = u.userid')
                       ->orderBy('logid', 'DESC');

        if ($date == '') {
            $this->database->where('title LIKE :keyw or entry LIKE :keyw');
            $params = array(
                'keyw' => "%{$kw}%"
            );
        } elseif ($kw == '') {
            $this->database->where('datec=:dates');
            $params = array(
                'dates' => $date
            );
        } else {
            $this->database->where('(title LIKE :keyw or entry LIKE :keyw) and datec=:dates');
            $params = array(
                'keyw' => "%{$kw}%",
                'dates' => $date
            );
        }

        return $this->database->get($params);
    }
}
