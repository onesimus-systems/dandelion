<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Repos;

use Dandelion\Repos\Interfaces;

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
        return (int) $this->database
            ->find($this->table)
            ->count();
    }

    private function fixLogFieldTypes(&$log)
    {
        $log['id'] = (int) $log['id'];
        $log['user_id'] = (int) $log['user_id'];
        $log['is_edited'] = (bool) $log['is_edited'];
        $log['num_of_comments'] = (int) $log['num_of_comments'];
    }

    public function getLogInfo($lid)
    {
        $logs = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->whereEqual($this->table.'.id', $lid)
            ->read($this->table.'.*, '.$this->prefix.'user.fullname');

        foreach ($logs as &$log) {
            $this->fixLogFieldTypes($log);
        }

        return $logs;
    }

    public function getLogList($offset, $limit)
    {
        $logs = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->orderDesc($this->table.'.id')
            ->limit(((int) $offset).','.((int) $limit))
            ->read($this->table.'.*, '.$this->prefix.'user.fullname');

        foreach ($logs as &$log) {
            $this->fixLogFieldTypes($log);
        }

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
            'category' => $cat,
            'is_edited' => 1
        ]);
    }

    public function getCountLogsBySearch($query)
    {
        $statement = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->orderDesc($this->table.'.id');

        list($params, $whereClause) = $this->buildSearchClause($query);

        $statement->where($whereClause, $params);

        return (int) $statement->count();
    }

    public function getLogsBySearch($query, $limit, $offset)
    {
        $statement = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->orderDesc($this->table.'.id')
            ->limit(((int) $offset).','.((int) $limit));

        list($params, $whereClause) = $this->buildSearchClause($query);

        $statement->where($whereClause, $params);

        $results = $statement->read($this->table.'.*, '.$this->prefix.'user.fullname');

        foreach ($results as &$log) {
            $this->fixLogFieldTypes($log);
        }

        return $results;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpressions)
     */
    private function buildSearchClause($query)
    {
        $params = [];
        $whereClause = '';

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

            if ($command === 'log') {
                // The log command has two placeholders in its
                // where clause. The rest only have one.
                $params []= "%{$struct['text']}%";
            }
        }

        return [$params, trim($whereClause, '& ')];
    }

    private function fixLogCommentFieldTypes(&$comment)
    {
        $comment['id'] = (int) $comment['id'];
        $comment['user_id'] = (int) $comment['user_id'];
        $comment['log_id'] = (int) $comment['log_id'];
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpressions)
     */
    public function getLogCommentsById($logid, $order = 'new')
    {
        $logs = $this->database
            ->find($this->prefix.'comment')
            ->belongsTo($this->prefix.'user', 'user_id')
            ->whereEqual($this->prefix.'comment.log_id', $logid);

        if ($order == 'new') {
            $logs = $logs->orderDesc($this->prefix.'comment.created');
        } else {
            $logs = $logs->orderAsc($this->prefix.'comment.created');
        }

        $results = $logs->read($this->prefix.'comment.*, '.$this->prefix.'user.fullname');

        foreach ($results as &$comment) {
            $this->fixLogCommentFieldTypes($comment);
        }

        return $results;
    }

    public function addComment($logid, $userid, $created, $text)
    {
        $commentAdded = $this->database->createItem($this->prefix.'comment', [
            'log_id' => $logid,
            'user_id' => $userid,
            'created' => $created,
            'comment' => $text
        ]);

        if ($commentAdded) {
            $currentCount = $this->numOfComments($logid);
            $this->database
                ->find($this->table)
                ->whereEqual('id', $logid)
                ->update(['num_of_comments' => $currentCount]);
        }

        return $commentAdded;
    }

    public function numOfComments($logid)
    {
        return (int) $this->database
                ->find($this->prefix.'comment')
                ->whereEqual('log_id', $logid)
                ->count();
    }
}
