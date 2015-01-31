<?php
/**
 *  Base module extended by all API modules
 */
namespace Dandelion\API\Module;

use \Dandelion\Application;
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

    public function __construct(Application $app, $ur, $urlParameters) {
        $this->app = $app;
        $this->ur = $ur;
        $this->up = $urlParameters;

        // Database type
        $dbtype = ucfirst($app->config['db']['type']);
        // Remove namespace from class
        $module = array_reverse(explode('\\', get_class($this)));
        // Remove the API at the end of the class name
        $module = substr($module[0], 0, -3);

        $repo = "\Dandelion\Repos\\{$dbtype}\\{$module}Repo";
        if (class_exists($repo)) {
            $this->repo = new $repo();
        } else {
            exit(ApiController::makeDAPI(6, 'Error initializing API request', 'api'));
        }
    }
}
