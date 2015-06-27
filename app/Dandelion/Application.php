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

use SC\SC;
use Exception;

use Dandelion\Utils\Updater;
use Dandelion\Storage\Loader;
use Dandelion\Utils\Configuration;
use Dandelion\Session\SessionManager;
use Dandelion\Exception\AbortException;
use Dandelion\Exception\ShutdownException;

use Onesimus\Router\Router;
use Onesimus\Router\Http\Request;
use Onesimus\Router\Http\Response;

use Onesimus\Logger\Logger;
use Onesimus\Logger\ErrorHandler;
use Onesimus\Logger\Adaptors\FileAdaptor;
use Onesimus\Logger\Adaptors\ChromeLoggerAdaptor;

/**
 * DandelionApplication represents an instance of Dandelion.
 */
class Application
{
    const VERSION = '6.0.0';
    const VER_NAME = 'Phoenix';

    /** @var array Paths for app, public, and root */
    public $paths = [];
    /** @var array Loaded configuration */
    public $config;
    /** @var Onesimus\Logger\Logger Main logger, goes to file */
    public $logger;
    /** @var Onesimus\Logger\Logger Debug logger, goes to Chrome Logger if debug enabled, otherwise null */
    public $debugLogger;
    /** @var Onesimus\Logger\ErrorHandler Handles errors, shutdown errors, and uncaught exceptions */
    private $errorHandler;
    /** @var Onesimus\Router\Http\Request HTTP object for current request */
    public $request;
    /** @var Onesimus\Router\Http\Response HTTP response object */
    public $response;
    /** @var Application Instance */
    private static $instance;

    public function __construct()
    {
        if (is_null(self::$instance)) {
            self::$instance = $this;
        }

        $this->request = Request::getRequest();
        $this->response = new Response();
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Load and run the application
     *
     * @return null
     */
    public function run()
    {
        // Load application configuration
        if (!$this->config = Configuration::load($this->paths['app'] . '/config/config.php')) {
            echo 'Please run the <a href="install.php">Installer</a>';
            exit();
        }

        // Register logging system
        $this->setupLogging();

        try {
            Updater::checkForUpdates($this);

            // Setup session manager
            SessionManager::register($this);
            SessionManager::startSession($this->config['cookiePrefix'].$this->config['phpSessionName']);

            // Setup routes and filters
            include $this->paths['app'] . '/routes.php';
            include $this->paths['app'] . '/filters.php';

            // The router uses this to determine the route
            // It's not always necassaily the right full URI
            $this->request->set('SERVER_NAME', $this->config['hostname']);

            $route = Router::route($this->request);
            $route->dispatch($this);
        } catch (ShutdownException $e) {
            // Just catch to continue output to client
        } catch (AbortException $e) {
            // Just die
            return;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->debugLogger->error($e->getMessage());

            $errorPage = new Controllers\PageController($this);
            $errorPage->renderErrorPage();
        }

        $this->sendToClient();
        return;
    }

    /**
     * Create and set main and debug loggers plus error handlers
     *
     * @return null
     */
    protected function setupLogging()
    {
        // Set PHP logging/error values
        error_reporting(E_ALL);
        ini_set('log_errors', true);
        ini_set('display_errors', $this->config['debugEnabled']);
        ini_set('display_startup_errors', $this->config['debugEnabled']);

        // Create a file adaptor for logs
        $fileAdaptor = new FileAdaptor($this->paths['logs'].'/logs.log');
        $this->logger = new Logger($fileAdaptor, 'mainlogger');

        // Create debug logger with null adaptor
        $this->debugLogger = new Logger();

        if ($this->config['debugEnabled']) {
            $fileAdaptor->separateLogFiles('.log');
            // Remove Null adaptor
            $this->debugLogger->removeAdaptor(0);
            // Add ChromeLogger to debug logger
            $this->debugLogger->addAdaptor(new ChromeLoggerAdaptor());
        } else {
            // Set minimum log level for non-debug
            $fileAdaptor->setLevel('warning');
        }

        // Register last ditch error functions
        $this->errorHandler = new ErrorHandler($this->logger);
        $this->errorHandler->registerErrorHandler();
        $this->errorHandler->registerShutdownHandler();
        $this->errorHandler->registerExceptionHandler();
    }

    /**
     * Add paths to Application path variable
     * @param  array  $paths Keyed array of paths
     * @param  bool $reset Reset the paths array to what's given
     * @return null
     */
    public function bindInstallPaths(array $paths, $reset = false)
    {
        if ($reset) {
            $this->paths = $paths;
        } else {
            $this->paths = array_merge($this->paths, $paths);
        }
        return;
    }

    /**
     * Return paths array statically
     *
     * @return array
     */
    public static function getPaths()
    {
        return self::$instance->paths;
    }

    /**
     * Send final output to client including headers, status, and body
     *
     * @return null
     */
    protected function sendToClient()
    {
        list($httpStatus, $httpHeaders, $httpBody) = $this->response->finalize();

        http_response_code($httpStatus);

        foreach ($httpHeaders as $header => $value) {
            header("{$header}: {$value}");
        }

        echo $httpBody;
    }
}
