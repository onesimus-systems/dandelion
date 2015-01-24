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

use Dandelion\Controllers\ApiController;

class rightsAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    public function getList() {
        $permissions = new \Dandelion\Permissions($this->db);
        $allGroups = $permissions->getGroupList();
        foreach ($allGroups as $key => $value) {
            $allGroups[$key]['permissions'] = unserialize($allGroups[$key]['permissions']);
        }
        return json_encode($allGroups);
    }

    public function getGroup() {
        $permissions = new \Dandelion\Permissions($this->db);
        $gid = $this->up->groupid;
        return json_encode(unserialize($permissions->getGroupList($gid)[0]['permissions']));
    }

    public function save() {
        if (!$this->ur->authorized('editgroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'rights'));
        }

        $permissions = new \Dandelion\Permissions($this->db);
        $gid = $this->up->groupid;
        $rights = (array) json_decode($this->up->rights);

        if ($permissions->editGroup($gid, $rights)) {
            return json_encode('User group saved');
        } else {
            exit(ApiController::makeDAPI(5, 'Error saving user group', 'rights'));
        }
    }

    public function create() {
        if (!$this->ur->authorized('addgroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'rights'));
        }

        $permissions = new \Dandelion\Permissions($this->db);
        $name = $this->up->name;
        $rights = (array) json_decode($this->up->rights);

        if (is_numeric($permissions->createGroup($name, $rights))) {
            return json_encode('User group created successfully');
        } else {
            exit(ApiController::makeDAPI(5, 'Error creating user group', 'rights'));
        }
    }

    public function delete() {
        if (!$this->ur->authorized('deletegroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'rights'));
        }

        $permissions = new \Dandelion\Permissions($this->db);
        $gid = $this->up->groupid;
        $users = $permissions->usersInGroup($gid);

        if (isset($users[0])) {
            return json_encode('This group is assigned to users.<br>Can not delete this group.');
        }
        else {
            $permissions->deleteGroup($gid);
            return json_encode('Group deleted successfully.');
        }
    }

    public function getUsersRights() {
        return json_encode($this->ur->getRightsForUser());
    }
}
