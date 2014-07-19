<?php
/**
 * Permissions is responsible for all permissions handling in Dandelion.
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
 * @date May 2014
 ***/
namespace Dandelion;

class permissions extends Database\dbManage
{
    /**
     * Get group data from database
     *
     * @param int $groupID - Group ID number, if omitted returns all groups
     * @return array
     */
    public function getGroupList($groupID = NULL)
    {
        if ($groupID === NULL) {
            $stmt = 'SELECT * FROM `'.DB_PREFIX.'rights`';
            $params = NULL;
        } else {
            $stmt = 'SELECT * FROM `'.DB_PREFIX.'rights` WHERE `id` = :id';
            $params = array(
                'id' => $groupID
            );
        }

        return $this->queryDB($stmt, $params);
    }

    /**
     * Save a new group to database
     *
     * @param string $name - Name of new group
     * @param array $rightsArray - Array containing rights
     * @return int - ID of new group
     */
    public function createGroup($name, $rightsArray)
    {
        $stmt = 'INSERT INTO `'.DB_PREFIX.'rights` (`role`,`permissions`) VALUES (:role, :rights)';
        $params = array(
            'role' => strtolower($name),
            'rights' => serialize($rightsArray)
        );

        $this->queryDB($stmt, $params);
        return $this->lastInsertId();
    }

    /**
     * Remove group from database
     *
     * @param int $id - ID number of group
     * @return bool - Status of query
     */
    public function deleteGroup($id)
    {
        $stmt = 'DELETE FROM `'.DB_PREFIX.'rights` WHERE id = :id';
        $params = array(
            'id' => $id
        );

        return $this->queryDB($stmt, $params);
    }

    /**
     * Save edits to group
     *
     * @param int $gid - Group ID number
     * @param array $rights - Group rights
     * @return bool - Status of query
     */
    public function editGroup($gid, $rights)
    {
        $rights = serialize($rights);

        $stmt = 'UPDATE `'.DB_PREFIX.'rights` SET `permissions` = :newPerm WHERE `id` = :gid';
        $params = array(
            'gid' => $gid,
            'newPerm' => $rights
        );

        return $this->queryDB($stmt, $params);
    }

    /**
     * Get only permissions array from database for group $userrole
     *
     * @param string $userrole - Name of permisions group
     * @return array - Permissions
     */
    public function loadRights($userrole)
    {
       $stmt = 'SELECT `permissions` FROM `'.DB_PREFIX.'rights` WHERE `role` = :userrole';
       $params = array(
            'userrole' => $userrole
       );

       return unserialize($this->queryDB($stmt, $params)[0]['permissions']);
    }

    /**
     * Determine if any users belong to group id $gid
     *
     * @param int $gid - Group ID number
     * @return array - Containing user IDs of users in group
     */
    public function usersInGroup($gid)
    {
        // Get name of group to search users table
        $groupName = $this->getGroupList($gid)[0]['role'];

        $stmt = 'SELECT `userid` FROM `'.DB_PREFIX.'users` WHERE `role` = :role';
        $params = array(
            'role' => $groupName
        );

        return $this->queryDB($stmt, $params);
    }
}
