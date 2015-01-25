<?php
/**
  * Routing system for Dandelion
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
  * The full GPLv3 license is available in LICENSE.md in the root.
  *
  * @author Lee Keitel
  * @date Jan 2015
***/
namespace Dandelion;

use \Dandelion\Auth\GateKeeper;

class Routes
{
    private static $routeList = [];

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function get($url, $options) {
        self::register('GET', $url, $options);
        return;
    }

    public static function post($url, $options) {
        self::register('POST', $url, $options);
        return;

    }

    public static function any($url, $options) {
        self::register('*', $url, $options);
        return;

    }

    private static function register($method, $url, $options) {
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

    public static function route($url) {
        $cleanUrl = self::cleanUrl($url);
        $httpmethod = $_SERVER['REQUEST_METHOD'];

        if (self::exactMatch($cleanUrl, $httpmethod)) {
            return true;
        }
        if (self::launchRoute($cleanUrl, $httpmethod)) {
            return true;
        }

        // No route found
        return false;
    }

    private static function exactMatch($url, $method) {
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

        $controller = new $routeClass();
        $controller->$routeMethod();
        return true;
    }

    private static function launchRoute($url, $method) {
        $url = explode('/', $url);
        $route = '';
        $pattern = [];

        foreach (self::$routeList as $key => $value) {
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
        $controller = new $routeClass();
        call_user_func_array(array($controller, $routeMethod), $params);

        return true;
    }

    private static function handleBefore($action) {
        if (!$action) {
            return true;
        }

        switch ($action) {
            case 'auth':
                return GateKeeper::authenticated();
        }

        return false;
    }

    private static function cleanUrl($url) {
        // Remove GET parameters
        $url = explode('?', $url)[0];
        return $url;
    }

    private static function getVars($pattern, $url) {
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
