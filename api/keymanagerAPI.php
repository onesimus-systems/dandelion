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

if (REQ_SOURCE != 'iapi') {
    exit(makeDAPI(2, 'This script can only be called by the internal API.', 'keyManager'));
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
    public static function getKey($db, $ur, $force = false) {
        $db->select('keystring')->from(DB_PREFIX.'apikeys')->where('user = :id');
        $params = array (
            "id" => $_SESSION['userInfo']['userid']
        );

        $key = $db->get($params);

        if (!empty($key[0]) && !$force) {
            return SELF::encodeKey($key[0]['keystring']);
        }
        else {
            // Clear database of old keys for user
            $db->delete()->from(DB_PREFIX.'apikeys')->where('user = :id');
            $params = array (
                "id" => $_SESSION['userInfo']['userid']
            );
            $db->go($params);

            // Generate new key
            $newKey = SELF::generateKey(40);

            // Insert new key
            $db->insert()->into(DB_PREFIX.'apikeys', array('keystring', 'user'))->values(array(':newkey', ':uid'));
            $params = array (
                "newkey" => $newKey,
                "uid" => $_SESSION['userInfo']['userid']
            );

            if ($db->go($params)) {
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
    public static function newKey($db, $ur) {
        return SELF::getKey($db, $ur, true);
    }

    /**
     * Put key into JSON encoded array with 'key' as the name
     *
     * @param string $key - API Key (or error message)
     */
    private static function encodeKey($key) {
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
    private static function generateKey($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
