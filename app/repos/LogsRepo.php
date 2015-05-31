<?php
/**
 * Repository for the Logs module
 */
namespace Dandelion\Repos;

use \Dandelion\Repos\Interfaces;

class LogsRepo extends BaseRepo implements Interfaces\LogsRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'log';
    }

    private $searchWhereClauses = [
        'title' => [
            'clause' => 'title %LIKE% ?',
            'like' => 'both'
        ],
        'body' => [
            'clause' => 'body %LIKE% ?',
            'like' => 'both'
        ],
        'log' => [
            'clause' => 'title %LIKE% ? %OR% body %LIKE% ?',
            'like' => 'both'
        ],
        'category' => [
            'clause' => 'category %LIKE% ?',
            'like' => 'right'
        ],
        'date' => [
            'clause' => 'date_created = ?',
            'like' => 'none'
        ],
        'daterange' => [
            'clause' => 'date_created >= ? && date_created <= ?',
            'like' => 'none'
        ],
        'datenotrange' => [
            'clause' => 'date_created < ? OR date_created > ?',
            'like' => 'none'
        ],
        'notdate' => [
            'clause' => 'date_created != ?',
            'like' => 'none'
        ],
        'afterondate' => [
            'clause' => 'date_created >= ?',
            'like' => 'none'
        ],
        'afterdate' => [
            'clause' => 'date_created > ?',
            'like' => 'none'
        ],
        'beforeondate' => [
            'clause' => 'date_created <= ?',
            'like' => 'none'
        ],
        'beforedate' => [
            'clause' => 'date_created < ?',
            'like' => 'none'
        ],
        'author' => [
            'clause' => '',
            'like' => 'none'
        ]
    ];

    public function numOfLogs()
    {
        return $this->database->find($this->table)->count();
    }

    public function getLogInfo($lid)
    {
        $log = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->whereEqual($this->table.'.id', $lid)
            ->read($this->table.'.*, '.$this->prefix.'user.fullname');

        return $log;
    }

    public function getLogList($offset, $limit)
    {
        $logs = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->orderDesc($this->table.'.id')
            ->limit(((int) $offset).','.((int) $limit))
            ->read($this->table.'.*, '.$this->prefix.'user.fullname');

        return $logs;
    }

    public function addLog($uid, $title, $body, $cat, $date, $time)
    {
        return $this->database->createItem($this->table, [
            'date_created' => $date,
            'time_created' => $time,
            'title' => $title,
            'body' => $body,
            'user_id' => $uid,
            'category' => $cat
        ]);
    }

    public function updateLog($lid, $title, $body, $cat)
    {
        return $this->database->updateItem($this->table, $lid, [
            'title' => $title,
            'body' => $body,
            'category' => $cat
        ]);
    }

    public function getLogsBySearch($query, $limit, $offset)
    {
        $params = [];
        $whereClause = '';
        $statement = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->orderDesc($this->table.'.id')
            ->limit(((int) $offset).','.((int) $limit));

        foreach ($query as $command => $struct) {
            $clause = $this->searchWhereClauses[$command]['clause'];

            // Special case for date ranges, they use arrays, not a single string
            if ($command == 'daterange' || $command == 'datenotrange') {
                $params []= $struct['text'][0];
                $params []= $struct['text'][1];
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
                    $params []= "%{$struct['text']}%";
                    break;
                case 'left':
                    $params []= "%{$struct['text']}";
                    break;
                case 'right':
                    $params []= "{$struct['text']}%";
                    break;
                default: // 'none' or invalid like type
                    $params []= $struct['text'];
                    break;
            }
        }

        $whereClause = trim($whereClause, '& ');
        $results = $statement->where($whereClause, $params)
            ->read($this->table.'.*, '.$this->prefix.'user.fullname');

        return $results;
    }
}
