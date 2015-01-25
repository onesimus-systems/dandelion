<?php
/**
 * Central entry point for Dandelion API. This script is responsible
 * for directing requests where needed.
 *
 * The DAPI array will always contain an error code. Please refer
 * to the documentation on the website or in the makeDAPI() function
 * for code meanings.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date Jan 2015
 */
namespace Dandelion\Controllers;

use \Dandelion\Auth\GateKeeper;
use \Dandelion\Storage\MySqlDatabase;

/**
 * This class represents a single API request. It's main public function is
 * processCall() which takes an array representing an exploded URL. It returns
 * nothing. Instead, the controller directly echos the API response to the client.
 */
class ApiController
{
    public function __construct() {
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
    public function apiCall($module, $method) {
        if ($_SESSION['app_settings']['public_api']) {
            $apikey = isset($_REQUEST['apikey']) ? $_REQUEST['apikey'] : '';
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
    public function internalApiCall($module, $method) {
        if (!GateKeeper::authenticated() && $module != 'auth') {
            exit(self::makeDAPI(3, 'Login required', 'iapi', '%REDIRECTLOGIN%'));
        }

        if ($module == 'auth') {
            $_SESSION['userInfo']['userid'] = null; // Triggers error on login because it's not initialized
        }
        $returnObj = (array) json_decode($this->processRequest($_SESSION['userInfo']['userid'], true, $module, $method));
        $returnObj['iapi'] = true;
        echo json_encode($returnObj);
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
    private function processRequest($key, $localCall, $module, $request) {
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
            }
            else {
                define('USER_ID', $key);
            }

            $userRights = new \Dandelion\rights(USER_ID);
        } else {
            $userRights = null;
        }

        $DatabaseConn = MySqlDatabase::getInstance();
        $urlParams = new \Dandelion\UrlParameters();

        // Call the requested function (as defined by the last part of the URL)
        $className = '\Dandelion\API\Module\\' . $module . 'API';
        $ApiModule = new $className($DatabaseConn, $userRights, $urlParams);

        if ($ApiModule instanceof \Dandelion\API\Module\BaseModule) {
            $data = $ApiModule->$request();
        } else {
            return self::makeDAPI(6, 'Internal Server Error', 'API', '');
        }

        // Return DAPI object
        return self::makeDAPI(0, 'Completed', $module, json_decode($data));
    }

    /**
     * Checks database to see if API is present and therefore valid
     *
     * @param string $key - API key to verify
     *
     * @return bool true on success, DAPI object on failure
     */
    private function verifyKey($key) {
        if (empty($key)) {
            // If $key is empty or the key isn't in the DB, exit with a DAPI object
            exit(self::makeDAPI(1, 'API key is not valid', 'api'));
        }

        $conn = \Dandelion\Storage\MySqlDatabase::getInstance();

        // Search for key with case sensitive collation
        $conn->select()
             ->from(DB_PREFIX.'apikeys')
             ->where('keystring = :key')
             ->collate('latin1_general_cs');
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
    private function apitest($key) {
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
     * @param string $subsystem - API where DAPI was created
     * @param array $data - Data returned from API
     *
     * @return JSON DAPI object
     */
    public static function makeDAPI($ecode, $status, $subsystem, $data = '') {
        /**
         * DAPI array composition:
         *
         * errorcode - Integer code corresponding to some error
         * status - String message of error or feedback
         * apisub - String name of the API subsystem that was called
         * data - Array/String of data returned by API subsystem
         *
         * Error Code Meanings:
         *
         * 0 - Successful API call
         * 1 - Invalid API key
         * 2 - Calling API subsystem from outside API
         * 3 - Action requires active login
         * 4 - Insufficient permissions
         * 5 - General error
         * 6 - Server error
         */
        $response = array (
            'errorcode' => $ecode,
            'status' => $status,
            'apisub' => $subsystem,
            'data' => $data
        );
        return json_encode($response);
    }
}
