<?php
/**
 * Handles API requests for rights management
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

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(ApiController::makeDAPI(2, 'This script can only be called by the API.', 'rights'));
}

class rightsAPI {
    public static function getList($db, $ur, $params) {
        $permissions = new \Dandelion\Permissions(\Dandelion\Storage\mySqlDatabase::getInstance());
        $allGroups = $permissions->getGroupList();
        foreach ($allGroups as $key => $value) {
            $allGroups[$key]['permissions'] = unserialize($allGroups[$key]['permissions']);
        }
        return json_encode($allGroups);
    }

    public static function getGroup($db, $ur, $params) {
        $permissions = new \Dandelion\Permissions(\Dandelion\Storage\mySqlDatabase::getInstance());
        $gid = $params->groupid;
        return json_encode(unserialize($permissions->getGroupList($gid)[0]['permissions']));
    }

    public static function save($db, $ur, $params) {
        $permissions = new \Dandelion\Permissions(\Dandelion\Storage\mySqlDatabase::getInstance());
        $gid = $params->groupid;
        $rights = (array) json_decode($params->rights);

        if ($permissions->editGroup($gid, $rights)) {
            return json_encode('User group saved');
        } else {
            exit(ApiController::makeDAPI(5, 'Error saving user group', 'rights'));
        }
    }

    public static function create($db, $ur, $params) {
        $permissions = new \Dandelion\Permissions(\Dandelion\Storage\mySqlDatabase::getInstance());
        $name = $params->name;
        $rights = (array) json_decode($params->rights);

        if (is_numeric($permissions->createGroup($name, $rights))) {
            return json_encode('User group created successfully');
        } else {
            exit(ApiController::makeDAPI(5, 'Error creating user group', 'rights'));
        }
    }

    public static function delete($db, $ur, $params) {
        $permissions = new \Dandelion\Permissions(\Dandelion\Storage\mySqlDatabase::getInstance());
        $gid = $params->groupid;
        $users = $permissions->usersInGroup($gid);

        if ($users[0]) {
            return json_encode('This group is assigned to users.<br>Can not delete this group.');
        }
        else {
            $permissions->deleteGroup($gid);
            return json_encode('Group deleted successfully.');
        }
    }

    public static function getUsersRights($db, $ur, $params) {
        return json_encode($ur->getRightsForUser());
    }
}
