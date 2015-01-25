<?php
/**
 * Manages api keys
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
 * @date January 2015
 */
namespace Dandelion;

use \Dandelion\Storage\Contracts\DatabaseConn;

class keyManager {
    public function __construct(DatabaseConn $db) {
        $this->db = $db;
        return;
    }

    public function getKey($uid, $force = false) {
        $this->db->select('keystring')->from(DB_PREFIX.'apikeys')->where('user = :id');
        $params = array (
            "id" => $uid
        );

        $key = $this->db->get($params);

        if (!empty($key[0]) && !$force) {
            return $key[0]['keystring'];
        }
        else {
            // Clear database of old keys for user
            $this->db->delete()->from(DB_PREFIX.'apikeys')->where('user = :id');
            $params = array (
                "id" => $uid
            );
            $this->db->go($params);

            // Generate new key
            $newKey = $this->generateKey(15);
            if (!$newKey) {
                return 'Error generating key.';
            }

            // Insert new key
            $this->db->insert()->into(DB_PREFIX.'apikeys', array('keystring', 'user'))->values(array(':newkey', ':uid'));
            $params = array (
                "newkey" => $newKey,
                "uid" => $uid
            );

            if ($this->db->go($params)) {
                return $newKey;
            }
            else {
                return 'Error generating key.';
            }
        }
    }

    public function revoke($uid) {
        $this->db->delete()
                 ->from(DB_PREFIX.'apikeys')
                 ->where('user = :id');
        $params = array(
            "id" => $uid
        );

        if ($this->db->go($params)) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Generate a random alphanumeric string
     *
     * @param int $length - Length of generated string
     *
     * @return string
     */
    private function generateKey($length = 10) {
        $bin = openssl_random_pseudo_bytes($length, $cstrong);
        if (!$cstrong && !$bin) {
            return '';
        }
        return bin2hex(base64_encode($bin));
    }
}
