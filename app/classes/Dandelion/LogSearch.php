<?php
/**
 * This class handles all log searching for Dandelion
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\LogsRepo;

class LogSearch
{
    private $repo;

    public function __construct(LogsRepo $repo)
    {
        $this->repo = $repo;
        return;
    }

    /**
     * Search logs using field:"query" syntax
     *
     * @param string $query - Query string to use for the new system
     *
     * @return array
     */
    public function search($query, $limit = 25, $offset = 0)
    {
        $parsedQuery = $this->parseSearchQuery($query);
        $matches = [];

        // Make sure at least one field isn't empty
        foreach ($parsedQuery as $value) {
            if ($value) {
                $matches = $this->repo->getLogsBySearch($parsedQuery, $limit, $offset);
                break;
            }
        }

        if (!$matches) {
            // Simple generic message saying no results were found
            $notFound = [[
                'canEdit' => false,
                'cat' => 'Not Found',
                'datec' => date('Y-m-d'),
                'edited' => 0,
                'entry' => 'Your search for `'.htmlentities($query).'` returned 0 results. Please check your query syntax and try again.',
                'logid' => -1,
                'realname' => 'Search',
                'timec' => date('H:i:s'),
                'title' => 'No Results',
                'usercreated' => '-1'
            ]];
            return $notFound;
        } else {
            return $matches;
        }
    }

    /**
     * Parses a search query into the individual field queries
     * @param  string
     * @return array
     */
    private function parseSearchQuery($query)
    {
        $parsedQuery = [];
        $fields = ['title', 'body', 'log', 'category', 'date', 'author'];

        $normalizedQuery = ' '.trim($query);

        foreach ($fields as $field) {
            if (strpos($normalizedQuery, ' '.$field.':') === false) {
                // If a query doesn't contain a field, there's no need to waste processing
                // on a regex match
                continue;
            }

            // This regex will capture a piece of text that contains characters matching:
            // [Not a \ or "] OR [\\] OR [\"] OR [\!]
            // So basically, any letter that's not a backslash or double quote, double
            // backslashes (for an escaped backslash) or a backslash double quote
            // (for an escaped double quote) or escaped ! to use at beginning of query
            $regex = "/\\s{$field}:\"(?P<querytext>(?:[^\\\\\"]|\\\\\\\\|\\\\\"|\\\\!)*?)\"/";
            preg_match($regex, $normalizedQuery, $match);
            $matchedText = $match['querytext'];

            // If a method is available for further processing, call it
            if (method_exists($this, $field.'Process')) {
                $methodName = $field.'Process';
                list($field, $matchedText) = $this->$methodName($field, $matchedText);
            }

            if (!is_array($matchedText)) {
                // Replace all instances of \\ with \\\\ in order to survive stripslashes()
                if (strpos($matchedText['text'], '\\\\') !== false) {
                    $matchedText['text'] = str_replace('\\\\', '\\\\\\\\', $matchedText['text']);
                }
                $matchedText['text'] = stripslashes($matchedText['text']);
            }

            $parsedQuery[$field] = $matchedText;
        }

        if (!$parsedQuery) {
            // If parsing failed for all fields, assume it's a general search
            // and recursivly run with the field 'log'
            $query = ($query[0] != '"') ? '"'.$query.'"' : $query;
            return $this->parseSearchQuery('log:'.$query);
        } else {
            return $parsedQuery;
        }
    }

    private function titleProcess($field, $data)
    {
        return $this->negateQuery($field, $data);
    }

    private function bodyProcess($field, $data)
    {
        return $this->negateQuery($field, $data);
    }

    private function logProcess($field, $data)
    {
        return $this->negateQuery($field, $data);
    }

    private function categoryProcess($field, $data)
    {
        return $this->negateQuery($field, $data);
    }

    private function negateQuery($field, $data)
    {
        $negate = false;
        if ($data[0] == '!') {
            $data = ltrim($data, '!');
            $negate = true;
        }
        return [$field, ['text' => $data, 'negate' => $negate]];
    }

    private function dateProcess($field, $data)
    {
        $data = strtolower($data);

        if (strpos($data, ' to ') !== false) {
            $range = explode(' to ', $data);
            if ($range[0][0] == '!') {
                $field = 'datenotrange';
                $range[0] = ltrim($range[0], '!');
            } else {
                $field = 'daterange';
            }
            $data = $range;

            foreach ($data as $key => $value) {
                $data[$key] = $this->formatDate($value);
            }
        } else {
            switch ($data[0]) {
                case '!':
                    $data = ltrim($data, '!');
                    $field = 'notdate';
                    break;
                case '>':
                    if ($data[1] == '=') {
                        $data = ltrim($data, '>=');
                        $field = 'afterondate';
                        break;
                    } else {
                        $data = ltrim($data, '>');
                        $field = 'afterdate';
                        break;
                    }
                case '<':
                    if ($data[1] == '=') {
                        $data = ltrim($data, '<=');
                        $field = 'beforeondate';
                        break;
                    } else {
                        $data = ltrim($data, '<');
                        $field = 'beforedate';
                        break;
                    }
            }
            $data = $this->formatDate($data);
        }

        return [$field, ['text' => $data, 'negate' => false]];
    }

    private function formatDate($datestr)
    {
        switch ($datestr) {
            case 'today':
                $datestr = date('Y-m-d');
                break;
            case 'yesterday':
                $datestr = date('Y-m-d', time()-(24*60*60));
            case 'last week':
                $datestr = date('Y-m-d', time()-(7*24*60*60));
            default:
                $datestr = date('Y-m-d', strtotime($datestr));
                break;
        }
        return $datestr;
    }
}
