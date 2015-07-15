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
    private $modules = [];

    /**
     * Registers a module and method with the api
     * @param  string/ApiModule $path    URL path to module OR an ApiModule object
     * @param  string $className    Classname of module
     * @param  array  $methods    Array of methods to add to module object
     */
    public function registerModule($path, $className = '', array $methods = [])
    {
        if ($path instanceof ApiModule) {
            $module = $path;
        } else {
            $module = new ApiModule($path, $className);
            $module->addMethods($methods);
            $this->modules[$path] = $module;
        }
    }

    /**
     * Returns if the module $module is registered
     * @param  string  $module Module name to check
     * @return boolean
     */
    public function hasModule($module)
    {
        return array_key_exists($module, $this->modules);
    }

    /**
     * Return the module named $module
     * @param  string $module Module to return
     * @return ApiModule OR null
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
     * @param  string $module Module name to check
     * @param  string $method Method name to check
     * @return boolean
     */
    public function pathExists($module, $method)
    {
        return ($this->hasModule($module) && $this->modules[$module]->hasMethod($method));
    }

    /**
     * [dispatchModule description]
     * @param  string $module Module to run
     * @param  string $path   Path mapped to method to call
     * @param  array  $args   Arguments to pass to module at construction
     * @return mixed
     */
    public function dispatchModule($module, $path, Request $request, array $args)
    {
        $response = '';

        if (!$this->hasModule($module)) {
            throw new ApiException('Module not found', 6);
        }

        $dispatching = $this->modules[$module];

        if (!$dispatching->hasMatchingMethod($path, $request)) {
            throw new ApiException('Bad API call', 5);
        }

        $className = $dispatching->getClass();
        $methodName = $dispatching->getMethod($path)->getMethod();

        if (!class_exists($className)) {
            throw new ApiException('Module not found', 6);
        }

        $reflectedClass = new \ReflectionClass($className);
        $ApiModule = $reflectedClass->newInstanceArgs($args);

        if (is_callable([$ApiModule, $methodName])) {
            try {
                $response = $ApiModule->$methodName();
            } catch (ApiException $e) {
                $e->setModule($module);
                throw $e;
            }
        } else {
            throw new ApiException('Bad API call', 5);
        }

        return $response;
    }
}
