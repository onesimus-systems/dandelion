<?php
/**
 * Handles key management for the public API
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

use Dandelion\database\dbManage;

if ($req_source != 'api') {
    exit('This script can only be called by the API.');
}

function encodeKey($key) {
   return json_encode(array("key" => $key));
}

function getKey($force = false) {
    $conn = new dbManage();
    
    $sql = 'SELECT keystring
            FROM '.DB_PREFIX.'apikeys
            WHERE user = :id';
    
    $params = array( "id" => $_SESSION['userInfo']['userid']);
    
    $key = $conn->queryDB($sql, $params);
    
    if (!empty($key[0]) && !$force) {
        return encodeKey($key[0]['keystring']);
    }
    else {
        $newKey = generateKey(40);
        
        // Clear database of old keys for user
        $sql = 'DELETE FROM '.DB_PREFIX.'apikeys
                WHERE user = :id';
        $params = array("id"=>$_SESSION['userInfo']['userid']);
        $conn->queryDB($sql, $params);
        
        $sql = 'INSERT INTO '. DB_PREFIX.'apikeys
                (keystring, user)
                VALUES (:newkey, :uid)';
        
        $params = array(
        	"newkey" => $newKey,
            "uid" => $_SESSION['userInfo']['userid']
        );
        
        if ($conn->queryDB($sql, $params)) {
            return encodeKey($newKey);
        }
        else {
            return encodeKey('Error generating key.');
        }
    }
}

function newKey() {
    return getKey(true);
}

function generateKey($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}