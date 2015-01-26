<?php
/**
 * Routing system for Dandelion
 */
namespace Dandelion;

use \Dandelion\Auth\GateKeeper;

class Routes
{
    private static $routeList = [];

    /**
     *  I feel Routes is a natural singleton object.
     *  There should be no need to have more than one global instance.
     */
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    /**
     *  Register route for a GET request
     */
    public static function get($url, $options)
    {
        self::register('GET', $url, $options);
        return;
    }

    /**
     *  Register a route for a POST request
     */
    public static function post($url, $options)
    {
        self::register('POST', $url, $options);
        return;

    }

    /**
     *  Register a route for any type of HTTP request
     */
    public static function any($url, $options)
    {
        self::register('*', $url, $options);
        return;

    }

    /**
     *  Common register function, adds route to $routeList
     */
    private static function register($method, $url, $options)
    {
        if (!is_array($options)) {
            $options = array('use' => $options, 'before' => '');
        }

        $options['use'] = explode('@', $options['use']);
        $url = self::cleanUrl($url);
        self::$routeList[$url] = array(
            'httpmethod' => $method,
            'class' => $options['use'][0],
            'method' => $options['use'][1],
            'pattern' => explode('/', $url),
            'before' => $options['before']
        );
        return;
    }

    /**
     *  Initiate the routing for the given URL
     */
    public static function route($url)
    {
        $cleanUrl = self::cleanUrl($url);
        $httpmethod = $_SERVER['REQUEST_METHOD'];

        $exact = self::exactMatch($cleanUrl, $httpmethod);
        if ($exact) {
            return $exact;
        }

        $best = self::launchRoute($cleanUrl, $httpmethod);
        if ($best) {
            return $best;
        }

        // No route found
        return false;
    }

    /**
     *  If the URL is an exact match a route, it gets precedence
     */
    private static function exactMatch($url, $method)
    {
        if (!isset(self::$routeList[$url])) {
            return false;
        }

        $routeHttpMethod = self::$routeList[$url]['httpmethod'];
        $routeClass = self::$routeList[$url]['class'];
        $routeMethod = self::$routeList[$url]['method'];

        if ($routeHttpMethod != '*' && $routeHttpMethod != $method) {
            return false;
        }

        if (!self::handleBefore(self::$routeList[$url]['before'])) {
            return false;
        }

        return array($routeClass, $routeMethod, []);
    }

    /**
     *  Search for the best fit route given the URL
     */
    private static function launchRoute($url, $method)
    {
        $url = explode('/', $url);
        $route = '';
        $pattern = [];

        foreach (self::$routeList as $key => $value) {
            // If the pattern is longer than the URL it won't match so just skip it
            // If the current candidate's pattern is longer than this iterations pattern,
            // chuck it as well. The longest pattern takes precedence.
            if (count($value['pattern']) > count($url) || count($value['pattern']) < count($pattern)) {
                continue;
            }

            $candidate = true;
            foreach ($value['pattern'] as $pkey => $pvalue) {
                if ($pvalue != $url[$pkey] && $pvalue[0] != '{') {
                    $candidate = false;
                    break;
                }
            }

            if ($candidate && ($value['httpmethod'] == $method || $value['httpmethod'] == '*')) {
                $route = $key;
                $pattern = $value['pattern'];
            }
        }

        if ($route === '') {
            return false;
        }

        $routeHttpMethod = self::$routeList[$route]['httpmethod'];
        $routeClass = self::$routeList[$route]['class'];
        $routeMethod = self::$routeList[$route]['method'];

        if (!self::handleBefore(self::$routeList[$route]['before'])) {
            return false;
        }

        $params = self::getVars($pattern, $url);
        return array($routeClass, $routeMethod, $params);
    }

    /**
     *  Perform any before actions on the route
     */
    private static function handleBefore($action)
    {
        if (!$action) {
            return true;
        }

        switch ($action) {
            case 'auth':
                return GateKeeper::authenticated();
        }

        return false;
    }

    /**
     *  Clean the URL by removing any get arguments from the end
     */
    private static function cleanUrl($url)
    {
        return explode('?', $url)[0];
    }

    /**
     *  If the route pattern has variables in it, find the corresponding
     *  positions in the URL and return those as arguments to the routed method
     */
    private static function getVars($pattern, $url)
    {
        $vars = [];
        foreach ($pattern as $key => $value) {
            if (!isset($value[0]) || $value[0] != '{') {
                continue;
            }

            $value = trim($value, '{}');
            array_push($vars, $url[$key]);
        }
        return $vars;
    }
}
