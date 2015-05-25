<?php
/**
 *  Base module extended by all API modules
 */
namespace Dandelion\API\Module;

use \Dandelion\Rights;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\Utils\Repos;
use \Dandelion\Exception\ApiException;
use \Dandelion\Controllers\ApiController;

abstract class BaseModule
{
    // Application
    protected $app;

    // User rights
    protected $ur;

    // URL parameters
    protected $up;

    // Repo for the specific module
    protected $repo;

    public function __construct(Application $app, Rights $ur, UrlParameters $urlParameters) {
        $this->app = $app;
        $this->ur = $ur;
        $this->up = $urlParameters;

        // Remove namespace
        $module = array_reverse(explode('\\', get_class($this)));
        // Remove the API at the end of the class name
        $module = substr($module[0], 0, -3);
        $this->repo = $this->makeRepo($module);
    }

    protected function makeRepo($module)
    {
        $repo = Repos::makeRepo($module);
        if ($repo) {
            return $repo;
        } else {
            throw new ApiException('Error initializing API request', 6);
        }
    }
}
