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

class ApiModuleMethod
{
    private $path = '';
    private $method = '';
    private $opts = [];

    /**
     * [__construct description]
     * @param string $path   URL path this method maps to
     * @param string $method The method to call for this path
     * @param array $opts   Options to set
     */
    public function __construct($path, $method, array $opts = [])
    {
        $this->path = $path;

        if (is_null($method)) {
            // Allows for using $path as the method name and still pass options
            // Just for convience
            $method = $path;
        }
        $this->method = $method;
        $this->setOpt($opts);
    }

    /**
     * Returns if this method matches the given path
     * @param  string $path Path to check
     * @return boolean
     */
    public function matches($path, Request $request)
    {
        if ($this->path != $path) {
            return false;
        }

        if ($http_method = $this->getOpt('http_method')) {
            $http_method = 'is'.ucfirst($http_method);
            if (!$request->$http_method()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns name of method mapped to this object
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set options for this method
     * @param array/string $name  Array of key-value pairs to set as options, or name of single option
     * @param mixed $value Value to set $name if not an array
     */
    public function setOpt($name, $value = null)
    {
        $newOpts = $name;
        // Convert a single option set to an array pair
        if (!is_array($name) && is_null($value)) {
            $newOpts = [$name => $value];
        }

        foreach ($newOpts as $key => $val) {
            $this->opts[$key] = $val;
        }
    }

    /**
     * Get option named $name
     * @param  string $name Options to return
     * @param  mixed $else Value to return if $name isn't set
     * @return mixed
     */
    public function getOpt($name = null, $else = null)
    {
        if (is_null($name)) {
            return $this->opts;
        } else {
            if (array_key_exists($name, $this->opts)) {
                return $this->opts[$name];
            }
        }
        return $else;
    }
}
