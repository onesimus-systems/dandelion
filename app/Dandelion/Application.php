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

use \SC\SC;
use \Exception;
use \Dandelion\Utils\Updater;
use \Dandelion\Storage\Loader;
use \Dandelion\Utils\Configuration;
use \Dandelion\Session\SessionManager;
use \Onesimus\Router\Router;
use \Onesimus\Router\Http\Request;

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
        if (!$this->config) {
            echo 'Please run the <a href="install.php">Installer</a>';
            exit();
        }
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

            $request = Request::getRequest();
            $request->set('SERVER_NAME', $this->config['hostname']);
            $route = Router::route($request);
            $route->dispatch($this);
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
