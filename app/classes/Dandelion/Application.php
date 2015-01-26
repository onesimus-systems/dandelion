<?php
/**
 * Main Dandelion application
 */
namespace Dandelion;

use \Dandelion\Routes;
use \Dandelion\Rights;
use \Dandelion\Logging;
use \Dandelion\Utils\Updater;
use \Dandelion\Auth\GateKeeper;
use \Dandelion\Utils\Configuration;
use \Dandelion\Storage\MySqlDatabase;
use \Dandelion\Session\SessionManager;

/**
 * DandelionApplication represents an instance of Dandelion.
 */
class Application
{
    public $url;
    public $paths = [];
    public $config;
    public $version = '6.0.0-dev';

    /**
     *  @param $url string - The request URI
     */
    public function __construct()
    {
        // Check for and apply updates
        //Updater::checkForUpdate();

        $this->url = $_SERVER['REQUEST_URI'];
    }

    /**
     * Main function of this class and single entrypoint into application.
     * Run takes the parsed URL and routes it to the appropiate place be it
     * the api controller or a page.
     */
    public function run()
    {
        // Register logging system
        Logging::register($this->paths['app'].'/logs');

        // Load application configuration
        $this->config = Configuration::load($this->paths);

        $this->loadLegacyCode();

        // Setup session manager
        SessionManager::register();
        SessionManager::startSession($this->config['phpSessionName']);

        $this->loadGlobalRights();

        include $this->paths['app'] . '/routes.php';
        list($class, $method, $params) = Routes::route($this->url);

        $controller = new $class($this);
        call_user_func_array(array($controller, $method), $params);
        return;
    }

    public function loadGlobalRights()
    {
        global $User_Rights;
        if (GateKeeper::authenticated()) {
            $User_Rights = new Rights($_SESSION['userInfo']['userid']);
        }
        return;
    }

    public function loadLegacyCode()
    {
        // Define constants
        define('DB_PREFIX', $this->config['db']['db_prefix']);
        define('D_VERSION', $this->version);
        define('PUBLIC_DIR', $this->paths['public']);
        define('THEME_DIR', 'assets/themes');
        define('DEFAULT_THEME', $this->config['defaultTheme']);
        define('PUBLIC_API', true);
        define('CHEESTO_ENABLED', true);

        // Give database class the info to connect
        MySqlDatabase::$connInfo = $this->config['db'];
        MySqlDatabase::$dbPrefix = $this->config['db']['db_prefix'];
        return;
    }

    public function bindInstallPaths(array $paths)
    {
        $this->paths = array_merge($this->paths, $paths);
        return;
    }
}
