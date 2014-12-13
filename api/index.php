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
 * @date Dec 2014
 */
namespace Dandelion\API;

use Dandelion\database\dbManage;
use Dandelion\Gatekeeper;

require_once '../lib/bootstrap.php';

// Process URL
$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
$url = explode('/', $url);

if ($url[0] == 'i') {
    internalApiCall($url);
} else {
    apiCall($url);
}

/**
 * Process api call
 * 
 * @param array $u - Exploded URL
 * 
 * @return Nothing
 */
function apiCall($u) {
    if ($_SESSION['app_settings']['public_api']) {
        $apikey = isset($_REQUEST['apikey']) ? $_REQUEST['apikey'] : '';
        echo processRequest($apikey, false, $u[0], $u[1]);
        session_write_close();
    }
    return;
}

/**
 * Process internal api call
 * 
 * @param array $u - Exploded URL
 * 
 * @return Nothing
 */
function internalApiCall($u) {
    if (!Gatekeeper\authenticated()) {
        exit('The internal API can only be accessed by Dandelion.');
    }

    $returnObj = (array) json_decode(processRequest($_SESSION['userInfo']['userid'], true, $u[1], $u[2]));
    $returnObj['iapi'] = true;
    echo json_encode($returnObj);
    session_write_close();
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
function processRequest($key, $localCall, $subsystem, $request) {
    if ($subsystem == 'apitest') {
        // Checks for a good API key and notifies requester
        return apitest($key);
    }

    /*
     * Declare request source as the api Default value is empty in bootstrap.php
     */
    if (!$localCall) {
        define('REQ_SOURCE', 'api'); // Public API
        define('USER_ID', verifyKey($key));
    }
    else {
        define('REQ_SOURCE', 'iapi'); // Internal API
        define('USER_ID', $key);
    }
    
    // Call the request function (as defined by the second part of the URL)
    $data = call_user_func(array (
        __NAMESPACE__ . '\\' . $subsystem . 'API',
        $request 
    ));
    
    // Return DAPI object
    return makeDAPI(0, 'Completed', $subsystem, json_decode($data));
}

/**
 * Checks database to see if API is present and therefore valid
 *
 * @param string $key - API key to verify
 *            
 * @return bool true on success, DAPI object on failure
 */
function verifyKey($key) {
    if (empty($key)) {
        // If $key is empty or the key isn't in the DB, exit with a DAPI object
        exit(makeDAPI(1, 'API key is not valid', 'index'));
    }

    $conn = new dbManage();
    
    // Search for key with case sensitive collation
    $sql = 'SELECT *
            FROM ' . DB_PREFIX . 'apikeys
            WHERE keystring = :key
            COLLATE latin1_general_cs';
    $params = array (
        "key" => $key 
    );
    
    $keyValid = $conn->queryDB($sql, $params);
    
    if (!empty($keyValid[0])) {
        return $keyValid[0]['user'];
    }
    return
}

/**
 * Test API key, used by extensions to verify key
 *
 * @param string $key - API key to test
 *            
 * @return DAPI object
 */
function apitest($key) {
    if (verifyKey($key)) {
        return makeDAPI(0, 'API key is good', 'index');
    }
    return
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
function makeDAPI($ecode, $status, $subsystem, $data = '') {
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
     */
    $response = array (
        'errorcode' => $ecode,
        'status' => $status,
        'apisub' => $subsystem,
        'data' => $data 
    );
    return json_encode($response);
}