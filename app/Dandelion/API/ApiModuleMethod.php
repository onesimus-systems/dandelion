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
use Dandelion\Utils\Validator;

use Onesimus\Router\Http\Request;

class ApiModuleMethod
{
    // URI location of this method
    private $path = '';
    // Name of method to call
    private $method = '';
    // Options for method such as HTTP method, URL values, etc.
    private $opts = [];

    /**
     * Create a new ApiModuleMethod
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
     * Returns if this method matches the given path and set options
     * @param  string $path Path to check
     * @return boolean | StdClass (validated data)
     */
    public function matches($path, Request $request)
    {
        // Check paths match
        if ($this->path != $path) {
            return false;
        }

        // Check if an http_method option was set and matches
        if ($http_method = strtolower($this->getOpt('http_method'))) {
            $http_method_func = 'is'.ucfirst($http_method);
            if (!$request->$http_method_func()) {
                return false;
            }
        }

        // Checks if parameters were set and validates
        if ($params = $this->getOpt('parameters')) {
            $batchValidations = [];
            $paramFunc = $http_method == 'get' ? 'get' : 'post';
            $paramFunc .= 'Param';
            foreach ($params as $id => $opts) {
                $default = isset($opts[0]) ? $opts[0] : null;
                $type = isset($opts[1]) ? $opts[1] : 'string';
                $priority = isset($opts[2]) ? $opts[2] : 'optional';

                $validation = [$request->$paramFunc($id, $default), $type, $priority];
                $batchValidations[$id] = $validation;
            }

            $validatedData = Validator::validateBatchKeys($batchValidations);
            if (!$validatedData->_valid) {
                throw new ApiException(
                    'Invalid parameters \''.implode(', ', $validatedData->_invalidFields).'\'',
                    ApiCommander::API_INVALID_CALL,
                    400);
            }
            return $validatedData;
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
        if (!is_array($name)) {
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
        }

        if (array_key_exists($name, $this->opts)) {
            return $this->opts[$name];
        }

        return $else;
    }
}
