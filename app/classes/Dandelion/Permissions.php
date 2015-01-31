<?php
/**
 * Rights group management
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\RightsRepo;

class Permissions
{
    public function __construct(RightsRepo $repo)
    {
        $this->repo = $repo;
        return;
    }
    /**
     * Get group data from database
     *
     * @param int $groupID - Group ID number, if omitted returns all groups
     * @return array
     */
    public function getGroupList($groupID = null)
    {
        if ($groupID === null) {
            return $this->repo->getAllGroupLists();
        } else {
            return $this->repo->getGroupList($groupID);
        }
    }

    /**
     * Save a new group to database
     *
     * @param string $name - Name of new group
     * @param array $rights - Array containing rights
     * @return int - ID of new group
     */
    public function createGroup($name, $rights)
    {
        $rights = serialize($rights);
        return $this->repo->createGroup(strtolower($name), $rights);
    }

    /**
     * Remove group from database
     *
     * @param int $id - ID number of group
     * @return bool - Status of query
     */
    public function deleteGroup($gid)
    {
        return $this->repo->deleteGroup($gid);
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
        return $this->repo->editGroup($gid, $rights);
    }

    /**
     * Get only permissions array from database for group $userrole
     *
     * @param string $userrole - Name of permisions group
     * @return array - Permissions
     */
    public function loadRights($role)
    {
       return unserialize($this->repo->loadRights($role));
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
        $groupName = $this->getGroupList($gid)['role'];

        return $this->repo->usersInGroup($groupName);
    }
}
