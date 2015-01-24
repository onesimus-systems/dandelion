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
namespace Dandelion\API\Module;

use Dandelion\API\ApiController;

class keyManagerAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    /**
     * Retrieve key from database for current user.
     * If a key isn't present, create one
     *
     * @param bool $force - Force a new key to be generated
     *
     * @return JSON - API Key or error message
     */
    public function getKey($force = false) {
        $key = new \Dandelion\keyManager($this->db);
        return SELF::encodeKey($key->getKey($_SESSION['userInfo']['userid'], $force));
    }

    /**
     * Called to force a new key to be generated
     */
    public function newKey() {
        return SELF::getKey(true);
    }

    public function revokeKey() {
        $userid = USER_ID;

        // Check permissions
        if (isset($this->up->uid)) {
            if ($this->ur->authorized('edituser') || $this->up->uid == USER_ID) {
                $userid = $this->up->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'keyManager'));
            }
        }

        $key = new \Dandelion\keyManager($this->db);
        return SELF::encodeKey($key->revoke($userid));
    }

    /**
     * Put key into JSON encoded array with 'key' as the name
     *
     * @param string $key - API Key (or error message)
     */
    private function encodeKey($key) {
        return json_encode(array (
            "key" => $key
        ));
    }
}
