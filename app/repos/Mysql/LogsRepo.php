<?php
/**
 * MySQL repository for the Logs module
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class LogsRepo extends BaseMySqlRepo implements Interfaces\LogsRepo
{
    private $searchWhereClauses = [
        'title' => [
            'clause' => 'title %LIKE% :title',
            'like' => 'both'
        ],
        'body' => [
            'clause' => 'entry %LIKE% :body',
            'like' => 'both'
        ],
        'log' => [
            'clause' => 'title %LIKE% :log %OR% entry %LIKE% :log',
            'like' => 'both'
        ],
        'category' => [
            'clause' => 'cat %LIKE% :category',
            'like' => 'right'
        ],
        'date' => [
            'clause' => 'datec = :date',
            'like' => 'none'
        ],
        'daterange' => [
            'clause' => 'datec >= :date1 && datec <= :date2',
            'like' => 'none'
        ],
        'datenotrange' => [
            'clause' => 'datec < :date1 OR datec > :date2',
            'like' => 'none'
        ],
        'notdate' => [
            'clause' => 'datec != :notdate',
            'like' => 'none'
        ],
        'afterondate' => [
            'clause' => 'datec >= :afterondate',
            'like' => 'none'
        ],
        'afterdate' => [
            'clause' => 'datec > :afterdate',
            'like' => 'none'
        ],
        'beforeondate' => [
            'clause' => 'datec <= :beforeondate',
            'like' => 'none'
        ],
        'beforedate' => [
            'clause' => 'datec < :beforedate',
            'like' => 'none'
        ],
        'author' => [
            'clause' => '',
            'like' => 'none'
        ]
    ];

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
                       ->limit(':offset,:lim');

        $params = [
            'offset' => ((int) $offset),
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

    public function updateLog($lid, $title, $body, $cat)
    {
        $this->database->update($this->prefix.'log')
                       ->set('title = :title, entry = :entry, cat = :cat, edited = 1')
                       ->where('logid = :lid');
        $params = [
            'title' => $title,
            'entry' => $body,
            'lid' => $lid,
            'cat' => $cat
        ];

        return $this->database->go($params);
    }

    public function getLogsBySearch($query, $limit, $offset)
    {
        $params = [];
        $whereClause = '';
        $this->database->select('l.*, u.realname')
                       ->from($this->prefix.'log AS l LEFT JOIN '.$this->prefix.'users AS u ON l.usercreated = u.userid')
                       ->orderBy('logid', 'DESC')
                       ->limit(':offset,:lim');

        foreach ($query as $command => $struct) {
            $clause = $this->searchWhereClauses[$command]['clause'];

            // Special case for date ranges, they use arrays, not a single string
            if ($command == 'daterange' || $command == 'datenotrange') {
                $params['date1'] = $struct['text'][0];
                $params['date2'] = $struct['text'][1];
                $whereClause .= $clause . ' && ';
                continue;
            }

            // Generate where clause, replacing logic variables with their appropiate values
            if ($struct['negate'] === true) {
                // Replace logic variables with their inverses
                $clause = str_replace('%LIKE%', 'NOT LIKE', $clause);
                $clause = str_replace('%OR%', 'AND', $clause);
                $clause = str_replace('%AND%', 'OR', $clause);
            } else {
                $clause = str_replace('%LIKE%', 'LIKE', $clause);
                $clause = str_replace('%OR%', 'OR', $clause);
                $clause = str_replace('%AND%', 'AND', $clause);
            }

            $whereClause .= $clause . ' && ';

            // Format paramter given the type used for LIKE
            switch ($this->searchWhereClauses[$command]['like']) {
                case 'both':
                    $params[$command] = "%{$struct['text']}%";
                    break;
                case 'left':
                    $params[$command] = "%{$struct['text']}";
                    break;
                case 'right':
                    $params[$command] = "{$struct['text']}%";
                    break;
                default: // 'none' or invalid like type
                    $params[$command] = $struct['text'];
                    break;
            }
        }

        $whereClause = trim($whereClause, '& ');
        $this->database->where($whereClause);
        $params['offset'] = (int) $offset;
        $params['lim'] = (int) $limit;

        return $this->database->get($params, \PDO::PARAM_INT);
    }
}
