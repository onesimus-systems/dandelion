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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date July 2014
 */
namespace Dandelion\API;

if (REQ_SOURCE != 'api') {
    exit(makeDAPI(2, 'This script can only be called by the API.', 'keyManager'));
}

class keyManagerAPI
{
    /**
     * Retrieve key from database for current user.
     * If a key isn't present, create one
     * 
     * @param bool $force - Force a new key to be generated
     * 
     * @return JSON - API Key or error message
     */
    public static function getKey($force = false) {
        $conn = new \Dandelion\database\dbManage();
        
        $sql = 'SELECT keystring
                FROM ' . DB_PREFIX . 'apikeys
                WHERE user = :id';
        
        $params = array (
            "id" => $_SESSION['userInfo']['userid'] 
        );
        
        $key = $conn->queryDB($sql, $params);
        
        if (!empty($key[0]) && !$force) {
            return SELF::encodeKey($key[0]['keystring']);
        }
        else {
            $newKey = SELF::generateKey(40);
            
            // Clear database of old keys for user
            $sql = 'DELETE FROM ' . DB_PREFIX . 'apikeys
                    WHERE user = :id';
            $params = array (
                "id" => $_SESSION['userInfo']['userid'] 
            );
            $conn->queryDB($sql, $params);
            
            $sql = 'INSERT INTO ' . DB_PREFIX . 'apikeys
                    (keystring, user)
                    VALUES (:newkey, :uid)';
            
            $params = array (
                "newkey" => $newKey,
                "uid" => $_SESSION['userInfo']['userid'] 
            );
            
            if ($conn->queryDB($sql, $params)) {
                return SELF::encodeKey($newKey);
            }
            else {
                return SELF::encodeKey('Error generating key.');
            }
        }
    }

    /**
     * Called to force a new key to be generated
     */
    public static function newKey() {
        return SELF::getKey(true);
    }

    /**
     * Put key into JSON encoded array with 'key' as the name
     * 
     * @param string $key - API Key (or error message)
     */
    public static function encodeKey($key) {
        return json_encode(array (
            "key" => $key 
        ));
    }

    /**
     * Generate a random alphanumeric string
     * 
     * @param int $length - Length of generated string
     * 
     * @return string
     */
    public static function generateKey($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i ++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}