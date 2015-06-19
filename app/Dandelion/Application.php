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

use \Onesimus\Logger\Logger;
use \Onesimus\Logger\Adaptors\FileAdaptor;

/**
 * DandelionApplication represents an instance of Dandelion.
 */
class Application
{
    const VERSION = '6.0.0';

    public $paths = [];
    public $config;
    public $logger;

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
        if (!$this->config = Configuration::load($this->paths['app'] . '/config/config.php')) {
            echo 'Please run the <a href="install.php">Installer</a>';
            exit();
        }
        $this->setConstants();

        // Register logging system
        $this->setupLogging();

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
            $this->logger->error($e->getMessage());
            $errorPage = new Controllers\PageController($this);
            $errorPage->renderErrorPage();
        }
        return;
    }

    protected function setConstants()
    {
        define('DEBUG_ENABLED', $this->config['debugEnabled']);
        define('PUBLIC_DIR', $this->paths['public']);
        return;
    }

    protected function setupLogging()
    {
        // Set PHP logging/error values
        error_reporting(E_ALL);
        ini_set('log_errors', true);
        ini_set('display_errors', $this->config['debugEnabled']);
        ini_set('display_startup_errors', $this->config['debugEnabled']);

        // Create a file adaptor for logs
        $fileAdaptor = new FileAdaptor($this->paths['logs'].'/logs.log');

        if ($this->config['debugEnabled']) {
            // Only in development do we want the logs separated, otherwise
            // they can stay together
            $fileAdaptor->fileLogLevels('emergency', $this->paths['logs'].'/emergency.log');
            $fileAdaptor->fileLogLevels('alert', $this->paths['logs'].'/alert.log');
            $fileAdaptor->fileLogLevels('critical', $this->paths['logs'].'/critical.log');
            $fileAdaptor->fileLogLevels('error', $this->paths['logs'].'/error.log');
            $fileAdaptor->fileLogLevels('warning', $this->paths['logs'].'/warning.log');
            $fileAdaptor->fileLogLevels('notice', $this->paths['logs'].'/notice.log');
            $fileAdaptor->fileLogLevels('info', $this->paths['logs'].'/info.log');
            $fileAdaptor->fileLogLevels('debug', $this->paths['logs'].'/debug.log');
        }

        // Create a logger
        $this->logger = new Logger($fileAdaptor);

        // Register last ditch error functions
        $this->logger->registerErrorHandler();
        $this->logger->registerShutdownHandler();
        $this->logger->registerExceptionHandler();
    }

    public function bindInstallPaths(array $paths, $reset = false)
    {
        if ($reset) {
            $this->paths = $paths;
        } else {
            $this->paths = array_merge($this->paths, $paths);
        }
        return;
    }

    public static function getPaths()
    {
        return self::$instance->paths;
    }
}
