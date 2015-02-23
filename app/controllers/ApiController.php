<?php
/**
 * Controller for public API
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Utils\Repos;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\API\Module\BaseModule;
use \Dandelion\Storage\MySqlDatabase;

class ApiController extends BaseController
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
    }

    /**
     * Process api call
     *
     * @param $module string - Name of api module to create
     * @param $method string - Method to call on module
     *
     * @return Nothing
     */
    public function apiCall($module, $method = '')
    {
        if ($this->app->config['publicApiEnabled']) {
            $urlParams = new UrlParameters();
            $apikey = $urlParams->get('apikey');
            echo $this->processRequest($apikey, false, $module, $method);
        }
        return;
    }

    /**
     * Process internal api call
     *
     * @param $module string - Name of api module to create
     * @param $method string - Method to call on module
     *
     * @return Nothing
     */
    public function internalApiCall($module, $method)
    {
        $returnObj = $this->processRequest($_SESSION['userInfo']['userid'], true, $module, $method);
        echo $returnObj;
        return;
    }

    /**
     * Process API request
     *
     * @param string $key - API key or userID
     * @param bool $localCall - Is the call from a Dandelion component
     * @param string $subsystem - Module being called
     * @param string $request - Method being called
     *
     * @return DAPI object
     */
    private function processRequest($key, $localCall, $module, $request)
    {
        if ($module == 'apitest') {
            // Checks for a good API key and notifies requester
            return $this->apitest($key);
        }

        /*
         * Declare request source as the api Default value is empty in bootstrap.php
         */
        if ($module != 'auth') {
            if (!$localCall) {
                define('USER_ID', $this->verifyKey($key));
            } else {
                define('USER_ID', $key);
            }

            $rightsRepo = Repos::makeRepo($this->app->config['db']['type'], 'Rights');
            $userRights = new Rights(USER_ID, $rightsRepo);
        } else {
            $userRights = null;
        }

        $DatabaseConn = MySqlDatabase::getInstance();
        $urlParams = new UrlParameters();

        // Call the requested function (as defined by the last part of the URL)
        $className = '\Dandelion\API\Module\\' . $module . 'API';
        if (!class_exists($className)) {
            return self::makeDAPI(6, 'Module not found', 'API', '');
        }
        $ApiModule = new $className($this->app, $userRights, $urlParams);

        if ($ApiModule instanceof BaseModule && method_exists($ApiModule, $request)) {
            $data = $ApiModule->$request();
        } else {
            return self::makeDAPI(5, 'Bad API call', 'API', '');
        }

        // Return DAPI object
        return self::makeDAPI(0, 'Completed', $module, $data);
    }

    /**
     * Checks database to see if API is present and therefore valid
     *
     * @param string $key - API key to verify
     *
     * @return bool true on success, DAPI object on failure
     */
    private function verifyKey($key)
    {
        if (empty($key)) {
            // If $key is empty or the key isn't in the DB, exit with a DAPI object
            exit(self::makeDAPI(1, 'API key is not valid', 'api'));
        }

        $conn = MySqlDatabase::getInstance();

        // Search for key with case sensitive collation
        $conn->select()
             ->from($this->app->config['db']['tablePrefix'].'apikeys')
             ->where('keystring = :key');
        $params = array (
            "key" => $key
        );

        $keyValid = $conn->get($params);

        if (!empty($keyValid[0])) {
            return $keyValid[0]['user'];
        } else {
            exit(self::makeDAPI(1, 'API key is not valid', 'api'));
        }
        return;
    }

    /**
     * Test API key, used by extensions to verify key
     *
     * @param string $key - API key to test
     *
     * @return DAPI object
     */
    private function apitest($key)
    {
        if ($this->verifyKey($key)) {
            return self::makeDAPI(0, 'API key is good', 'api');
        } else {
            return self::makeDAPI(1, 'Invalid API key', 'api');
        }
    }

    /**
     * Generate and return a JSON encoded 'DAPI' object
     *
     * @param int $ecode - Error code
     * @param string $status - Text status message
     * @param string $module - API where DAPI was created
     * @param array $data - Data returned from API
     *
     * @return JSON DAPI object
     */
    public static function makeDAPI($ecode, $status, $module, $data = '')
    {
        /**
         * DAPI array composition:
         *
         * errorcode - Integer code corresponding to some error
         * status - String message of error or feedback
         * module - String name of the API module that was called
         * data - Array/String of data returned by API module
         *
         * Error Code Meanings:
         *
         * 0 - Successful API call
         * 1 - Invalid API key
         * 2 - Reserved for future use
         * 3 - Action requires active login
         * 4 - Insufficient permissions
         * 5 - General error
         * 6 - Server error
         */
        $response = array (
            'errorcode' => $ecode,
            'status' => $status,
            'module' => $module,
            'data' => $data
        );
        return json_encode($response);
    }
}
