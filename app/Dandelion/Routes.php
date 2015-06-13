<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
  */
namespace Dandelion;

use \Dandelion\Auth\GateKeeper;
use \Dandelion\Utils\Configuration;

class Routes
{
    private static $routeList = [];
    private static $filters = [];

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
     * Register a group of functions
     */
    public static function group(array $properties, array $routes)
    {
        // $routes: [0] = HTTP method, [1] = pattern, [2] = controller/method route
        $baseProperties = ['filter' => '', 'prefix' => '', 'rprefix' => ''];
        $properties = array_merge($baseProperties, $properties);

        foreach ($routes as $route) {
            if (!method_exists(__CLASS__, $route[0])) {
                continue;
            }

            $options = [
                'use' => $properties['rprefix'].$route[2],
                'filter' => $properties['filter']
            ];
            $route[1] = $properties['prefix'].$route[1];
            self::$route[0]($route[1], $options);
        }
    }

    /**
     *  Common register function, adds route to $routeList
     */
    private static function register($method, $url, $options)
    {
        if (!is_array($options)) {
            $options = ['use' => $options, 'filter' => ''];
        }

        $options['use'] = explode('@', $options['use']);
        $url = self::cleanUrl($url);
        $routeName = $url.'@'.$method;
        self::$routeList[$routeName] = [
            'httpmethod' => $method,
            'class' => $options['use'][0],
            'method' => $options['use'][1],
            'pattern' => explode('/', $url),
            'filter' => $options['filter']
        ];
        return;
    }

    /**
     * Get array of current routes
     */
    public static function getRoutes()
    {
        return self::$routeList;
    }

    /**
     *  Initiate the routing for the given URL
     */
    public static function route()
    {
        $config = Configuration::getConfig();

        // Build the full path in order to filter out the defined hostname
        $scheme = !isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http' : 'https';
        $serverName = $_SERVER['SERVER_NAME'];
        $requestUri = self::cleanUrl($_SERVER['REQUEST_URI']);
        $fullUrl = $scheme . '://' . $serverName . $requestUri;

        // Get the requested internal URI
        $path = str_replace(rtrim($config['hostname'], '/'), '', $fullUrl);

        $httpmethod = $_SERVER['REQUEST_METHOD'];

        $exact = self::exactMatch($path, $httpmethod);
        if ($exact) {
            return $exact;
        }

        $best = self::launchRoute($path, $httpmethod);
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
        $routeName = $url.'@'.$method;
        if (!isset(self::$routeList[$routeName])) {
            return false;
        }

        $routeHttpMethod = self::$routeList[$routeName]['httpmethod'];
        $routeClass = self::$routeList[$routeName]['class'];
        $routeMethod = self::$routeList[$routeName]['method'];

        if ($routeHttpMethod != '*' && $routeHttpMethod != $method) {
            return false;
        }

        if (!self::handleFilter(self::$routeList[$routeName]['filter'])) {
            return false;
        }

        return [$routeClass, $routeMethod, []];
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
                if ($pvalue != $url[$pkey] && substr($pvalue, 0, 1) != '{') {
                    $candidate = false;
                    break;
                }
            }

            if ($candidate && ($value['httpmethod'] == $method || $value['httpmethod'] == '*')) {
                $route = $key;
                $pattern = $value['pattern'];
            }
        }

        if (!$route) {
            return false;
        }

        $routeHttpMethod = self::$routeList[$route]['httpmethod'];
        $routeClass = self::$routeList[$route]['class'];
        $routeMethod = self::$routeList[$route]['method'];

        if (!self::handleFilter(self::$routeList[$route]['filter'])) {
            return false;
        }

        $params = self::getVars($pattern, $url);
        return [$routeClass, $routeMethod, $params];
    }

    public static function filter($name, \Closure $callback)
    {
        self::$filters[$name] = $callback;
    }

    /**
     *  Perform any before actions on the route
     */
    private static function handleFilter($action)
    {
        if (!$action) {
            return true;
        }

        if (array_key_exists($action, self::$filters)) {
            $callback = self::$filters[$action];
            return $callback();
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

            array_push($vars, $url[$key]);
        }
        return $vars;
    }
}
