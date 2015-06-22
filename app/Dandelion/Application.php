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

use Onesimus\Router\Router;
use Onesimus\Router\Http\Request;

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

    public $paths = [];
    public $config;
    public $logger;
    public $debugLogger;
    private $errorHandler;

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

    public static function getInstance()
    {
        return self::$instance;
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

        // Register logging system
        $this->setupLogging();

        try {
            // Setup session manager
            SessionManager::register($this);
            SessionManager::startSession($this->config['cookiePrefix'].$this->config['phpSessionName']);

            // Setup routes and filters
            include $this->paths['app'] . '/routes.php';
            include $this->paths['app'] . '/filters.php';

            $this->debugLogger->debug($_SERVER);
            $request = Request::getRequest();
            $request->set('SERVER_NAME', $this->config['hostname']);
            $this->debugLogger->debug($request->get('SERVER_NAME'));
            $this->debugLogger->debug($request->get('FULL_URI'));

            $route = Router::route($request);
            $route->dispatch($this);
        } catch(Exception $e) {
            $this->logger->error($e->getMessage());
            $this->debugLogger->error($e);

            $errorPage = new Controllers\PageController($this);
            $errorPage->renderErrorPage();
        }
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
