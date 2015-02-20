<?php
/**
 * Main Dandelion application
 */
namespace Dandelion;

use \Dandelion\Utils\Updater;
use \Dandelion\Utils\Configuration;
use \Dandelion\Storage\MySqlDatabase;
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
        if (is_null($instance)) {
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
        // Register logging system
        Logging::register($this, $this->paths['app'].'/logs');

        // Load application configuration
        $this->config = Configuration::load($this->paths);

        $this->loadLegacyCode();

        // Setup session manager
        SessionManager::register($this);
        SessionManager::startSession($this->config['phpSessionName']);

        include $this->paths['app'] . '/routes.php';
        include $this->paths['app'] . '/filters.php';
        list($class, $method, $params) = Routes::route();

        if (!$class || !class_exists($class)) {
            Logging::errorPage("Controller '{$class}' wasn't found.");
            return;
        }

        $controller = new $class($this);
        if (method_exists($controller, $method)) {
            call_user_func_array(array($controller, $method), $params);
        } else {
            Logging::errorPage("Method '{$method}' wasn't found in Class '{$class}'.");
        }
        return;
    }

    public function loadLegacyCode()
    {
        // Give database class the info to connect
        MySqlDatabase::getInstance($this->config['db'], true);

        // Define constants
        define('DEBUG_ENABLED', $this->config['debugEnabled']);
        define('PUBLIC_DIR', $this->paths['public']);
        define('THEME_DIR', 'assets/themes');
        define('DEFAULT_THEME', $this->config['defaultTheme']);
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
