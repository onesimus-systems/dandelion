<?php
/**
 * Main Dandelion application
 */
namespace Dandelion;

use \SC\SC;
use \Exception;
use \Dandelion\Utils\Updater;
use \Dandelion\Storage\Loader;
use \Dandelion\Utils\Configuration;
use \Dandelion\Session\SessionManager;

/**
 * DandelionApplication represents an instance of Dandelion.
 */
class Application
{
    const VERSION = '6.0.0';

    public $paths = [];
    public $config;

    private static $instance;

    /**
     *
     */
    public function __construct()
    {
        // Check for and apply updates
        //Updater::checkForUpdate();
        if (is_null(self::$instance)) {
            self::$instance = $this;
        }
    }

    /**
     * Main function of this class and single entrypoint into application.
     * Run takes the parsed URL and routes it to the appropiate place be it
     * the api controller or a page.
     */
    public function run()
    {
        // Load application configuration
        $this->config = Configuration::load($this->paths);
        $this->setConstants();

        // Register logging system
        Logging::register($this, $this->paths['app'].'/logs');

        try {
            // Setup session manager
            SessionManager::register($this);
            SessionManager::startSession($this->config['cookiePrefix'].$this->config['phpSessionName']);

            // Setup routes and filters
            include $this->paths['app'] . '/routes.php';
            include $this->paths['app'] . '/filters.php';

            // Get route for request
            list($class, $method, $params) = Routes::route();

            // Check controller exists
            if (!$class || !class_exists($class)) {
                Logging::errorPage("Controller '{$class}' wasn't found.");
                return;
            }

            // Check controller has method for request
            $controller = new $class($this);
            if (method_exists($controller, $method)) {
                call_user_func_array(array($controller, $method), $params);
            } else {
                Logging::errorPage("Method '{$method}' wasn't found in Class '{$class}'.");
            }
        } catch(Exception $e) {
            Logging::errorPage($e);
        }
        return;
    }

    public function setConstants()
    {
        define('DEBUG_ENABLED', $this->config['debugEnabled']);
        define('PUBLIC_DIR', $this->paths['public']);
        return;
    }

    public function bindInstallPaths(array $paths)
    {
        $this->paths = array_merge($this->paths, $paths);
        return;
    }

    public static function getPaths()
    {
        return self::$instance->paths;
    }
}
