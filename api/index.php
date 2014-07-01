<?php
/**
 * Central entry point for Dandelion API. This script is responsible
 * for directing requests where needed.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date July 2014
 ***/

namespace Dandelion\API;

use Dandelion\database\dbManage;

require '../scripts/bootstrap.php';

if ($_SESSION['app_settings']['public_api']) {
    /* Declare request source as the api
       Default set to empty in bootstrap.php */
    $req_source = 'api';
    
    $url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
    $url = explode('/', $url);
    
    if ($url[0] != 'keyManager') {
        verifyKey($_REQUEST['apikey']);
    }
    
    include "{$url[0]}API.php";
    
    echo $url[1]();
}

function verifyKey($key) {
    $conn = new dbManage();
    
    // Search for key with case sensitive collation
    $sql = 'SELECT *
            FROM '.DB_PREFIX.'apikeys
            WHERE keystring = :key
            COLLATE latin1_general_cs';
    $params = array("key"=>$key);
    
    $keyValid = $conn->queryDB($sql, $params);
    
    if (empty($keyValid[0])) {
        // No API key is present in the database
        // matching supplied key
        exit('API key is not valid');
    }
}