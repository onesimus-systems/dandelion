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

class ApiCommander
{
    const API_SUCCESS = 0;
    const API_INVALID_KEY = 1;
    const API_DISABLED = 2;
    const API_LOGIN_REQUIRED = 3;
    const API_INSUFFICIENT_PERMISSIONS = 4;
    const API_GENERAL_ERROR = 5;
    const API_SERVER_ERROR = 6;
    const API_INVALID_CALL = 7;
    const API_MODULE_NOT_FOUND = 8;
    const API_MODULE_METHOD_NOT_FOUND = 9;
    const API_CHEESTO_DISABLED = 10;

    // path -> module keyed array of registered modules
    private $modules = [];

    /**
     * Registers a module and method with the api
     * @param  string/ApiModule $path    URL path to module OR an ApiModule object
     * @param  string $className    Classname of module
     * @param  array  $methods    Array of methods to add to module object
     * @return void
     */
    public function registerModule($path, $className = '', array $methods = [])
    {
        if ($path instanceof ApiModule) {
            // Function was given an already created ApiModule object
            $module = $path;
        } else {
            // ApiModule needs to be created
            $module = new ApiModule($path, $className);
            $module->addMethods($methods);
        }
        // Add module to list
        $this->modules[$module->getPath()] = $module;
    }

    /**
     * Returns if the module $module is registered
     * @param  string  $module Module name to check (base path)
     * @return boolean
     */
    public function hasModule($module)
    {
        return array_key_exists($module, $this->modules);
    }

    /**
     * Return the module named $module
     * @param  string $module Module to return (base path)
     * @return ApiModule | null
     */
    public function getModule($module)
    {
        if ($this->hasModule($module)) {
            return $this->modules[$module];
        }
        return null;
    }

    /**
     * Returns if $module exists with $method registered
     * @param  string $module Module name to check (base path)
     * @param  string $method Method name to check
     * @return boolean
     */
    public function pathExists($module, $method)
    {
        return ($this->hasModule($module) && $this->modules[$module]->hasMethod($method));
    }

    /**
     * Dispatch the module and call the appropiate method for the request
     * @param  string $module Module to run
     * @param  string $path   Path mapped to method to call
     * @param  array  $args   Arguments to pass to module at construction
     * @return mixed
     */
    public function dispatchModule($module, $path, Request $request, array $args)
    {
        $response = '';

        // Check module exists
        if (!$this->hasModule($module)) {
            throw new ApiException('Module not found', self::API_MODULE_NOT_FOUND);
        }

        $dispatching = $this->modules[$module];
        // Check module has a path for the requested URL
        // Returning request data to be injected
        $injectData = $dispatching->hasMatchingMethod($path, $request);
        if ($injectData === false) {
            throw new ApiException('Module doesn\'t have method', self::API_MODULE_METHOD_NOT_FOUND);
        }

        // Get the names of the class and method to call
        $className = $dispatching->getClass();
        $methodName = $dispatching->getMethod($path, $request->getMethod())->getMethod();

        if (!class_exists($className)) {
            throw new ApiException('Module not found', self::API_MODULE_NOT_FOUND);
        }

        // Create a new class using $args as the parameters
        $reflectedClass = new \ReflectionClass($className);
        $ApiModule = $reflectedClass->newInstanceArgs($args);

        // Check the method is available
        if (!is_callable([$ApiModule, $methodName])) {
            throw new ApiException('Controller method is not callable', self::API_MODULE_METHOD_NOT_FOUND);
        }

        // Attempt calling the method
        try {
            $response = $ApiModule->$methodName($injectData);
        } catch (ApiException $e) {
            $e->setModule($module);
            throw $e;
        }

        return $response;
    }
}
