<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API;

use Dandelion\Exception\ApiException;

use Onesimus\Router\Http\Request;

class ApiModule
{
    private $path;
    private $class;
    private $methods = [];

    /**
     * [__construct description]
     * @param string $path  URL path
     * @param string $class Class to create when dispatching
     */
    public function __construct($path, $class)
    {
        $this->path = $path;
        $this->class = $class;
    }

    public function getPath() {
        return $this->path;
    }

    /**
     * Add method mappings to the module
     * @param array  $methods Array of path -> method pairs. An element that's an array of count 1 or
     *    that's a string will be assumed that the value is both the path and method name.
     *    A 1 length array can also be an ApiModuleMethod object, in which case it will added as is.
     *    Ex: [["path1", "method1"], ["path2"], "path3"] evaluates to
     *    Path "path1" calls "method1", "path2" called method named "path2", "path3" calls "path3".
     */
    public function addMethods(array $methods)
    {
        foreach ($methods as $method) {
            if (!is_array($method)) {
                $method = [$method];
            }

            switch (count($method)) {
                case 1:
                    $this->methods[$method[0]] = $method instanceof ApiModuleMethod ? $method : new ApiModuleMethod($method[0], $method[0]);
                    break;
                case 2:
                    $this->methods[$method[0]] = new ApiModuleMethod($method[0], $method[1]);
                    break;
                case 3:
                    $this->methods[$method[0]] = new ApiModuleMethod($method[0], $method[1], $method[2]);
                    break;
            }
        }
    }

    /**
     * Returns the class name mapped to this module
     * @return string Class name for module
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Returns if the module has a method named $method
     * @param  string  $method Method name to check for
     * @return boolean
     */
    public function hasMethod($method)
    {
        return array_key_exists($method, $this->methods);
    }

    /**
     * Return a method object associated with this module named $method
     * @param  string $method Method name to return
     * @return ApiModuleMethod OR null
     */
    public function getMethod($method)
    {
        if ($this->hasMethod($method)) {
            return $this->methods[$method];
        }
        return null;
    }

    /**
     * hasMatchingMethod checks if this module has a method that matches an incoming request.
     * The call to the method's matches() will validate any options for the method and return false
     * if all options aren't met.
     * @param  string  $path    URI path requested
     * @param  Request $request HTTP request information
     * @return boolean
     */
    public function hasMatchingMethod($path, Request $request)
    {
        if ($this->hasMethod($path)) {
            return $this->methods[$path]->matches($path, $request);
        }
        return false;
    }
}
