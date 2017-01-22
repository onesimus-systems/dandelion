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

use Exception;

use Dandelion\Utils\Configuration as Config;
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
    /**
     * @const string Current version number
     */
    const VERSION = '6.1.1';

    /**
     * @const string Current version name
     */
    const VER_NAME = 'Phoenix';

    /**
     * Base file paths used throughout the application
     * @var array
     */
    public $paths = [];
    /**
     * Main logger, goes to a single file
     * @var Onesimus\Logger\Logger
     */
    public $logger;
    /**
     * Debug logger, goes to Chrome Logger if debug enabled, otherwise null
     * @var Onesimus\Logger\Logger
     */
    public $debugLogger;
    /**
     * HTTP object for current request
     * @var Onesimus\Router\Http\Request
     */
    public $request;
    /**
     * HTTP response object
     * @var Onesimus\Router\Http\Response
     */
    public $response;

    /**
     * Application instance
     * @var Application
     * @access private
     * @static
     */
    private static $instance;
    /**
     * Handles errors, shutdown errors, and uncaught exceptions
     * @var Onesimus\Logger\ErrorHandler
     * @access private
     */
    private $errorHandler;

    /**
     * Create object instance
     * @param Request $req Incoming HTTP request
     * @return void
     */
    private function __construct(Request $req)
    {
        $this->request = $req;
        $this->response = new Response();
    }

    /**
     * Get instance of Application
     * @param  Request $req Incoming HTTP request, forwarded to constructor. This is only needed at application launch.
     * @return Application Current instance
     */
    public static function getInstance(Request $req = null)
    {
        if (is_null(self::$instance)) {
            if (is_null($req)) {
                throw new \InvalidArgumentException('A request object must be passed to the application.');
            }
            self::$instance = new Application($req);
        }

        return self::$instance;
    }

    /**
     * Setup and run the application
     * @return void
     */
    public function run()
    {
        $startTime = microtime(true);

        // Load application configuration
        if (!Config::load($this->paths['app'] . '/config')) {
            echo 'Please run the <a href="install.php">Installer</a>';
            exit();
        }

        // Register logging system
        $this->setupLogging();

        try {
            // Setup session manager
            SessionManager::register();
            SessionManager::startSession(Config::get('cookiePrefix').Config::get('phpSessionName'));

            // Setup routes and filters
            include $this->paths['app'] . '/routes.php';
            include $this->paths['app'] . '/filters.php';

            $this->request->set('REQUEST_URI', $this->getRealRequestURI());

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

            $errorPage = new Controllers\PageController($this, false);
            $errorPage->renderErrorPage();
        }

        $endTime = round(((microtime(true) - $startTime) * 1000), 2);
        $this->response->headers->set('X-Dandelion-Request-Time', $endTime.'ms');

        $this->sendToClient();
        return;
    }

    /**
     * Create and set main and debug loggers plus error handlers
     * @return void
     */
    protected function setupLogging()
    {
        // Set PHP logging/error values
        error_reporting(E_ALL);
        ini_set('log_errors', true);
        ini_set('display_errors', Config::get('debugEnabled'));
        ini_set('display_startup_errors', Config::get('debugEnabled'));

        // Create a file adaptor for logs
        $fileAdaptor = new FileAdaptor($this->paths['logs'].'/logs.log');
        $this->logger = new Logger($fileAdaptor, 'mainlogger');

        // Create debug logger with null adaptor
        $this->debugLogger = new Logger();

        if (Config::get('debugEnabled')) {
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
     * @param array $paths keyed array of base file paths
     * @param bool $reset Reset the paths array to what's given
     * @return void
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
     * Get current base filepaths
     * @return array
     */
    public static function getPaths()
    {
        return self::$instance->paths;
    }

    /**
     * Send final output to client including headers, status, and body
     * @return void
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

    protected function getRealRequestURI()
    {
        $r = $this->request;
        // Get the base URI from the hostname
        preg_match("~https?://.*?/(.*)~", Config::get('hostname'), $subDirMatch);
        // Get the request URI as set the web server
        $realRequestUri = $r->get('REQUEST_URI');
        // If there's a base URI in the hostname, deal with it
        if (count($subDirMatch) > 0) {
            // Chop off the base URI from the given URI
            $realRequestUri = substr($realRequestUri, strlen($subDirMatch[1])+1);
            // If for some reason substr returns false or an empty string, make it the root path
            if ($realRequestUri === false) {
                $realRequestUri = '/';
            }
        }
        return $realRequestUri;
    }
}
