<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion;

use Dandelion\Repos\Interfaces\GroupsRepo;

class Groups
{
    protected $repo;

    private $defaultPermissions = [
        'createlog' => false,
        'editlog' => false,
        'editlogall' => false,
        'viewlog' => false,
        'addcomment' => false,

        'createcat' => false,
        'editcat' => false,
        'deletecat' => false,

        'createuser' => false,
        'edituser' => false,
        'deleteuser' => false,

        'creategroup' => false,
        'editgroup' => false,
        'deletegroup' => false,

        'viewcheesto' => false,
        'updatecheesto' => false,

        'admin' => false
    ];

    private $permissionNames = [
        'createlog' => 'Create logs',
        'editlog' => 'Edit logs created by user',
        'editlogall' => 'Edit any log',
        'viewlog' => 'View logs',
        'addcomment' => 'Add Comments',

        'createcat' => 'Create categories',
        'editcat' => 'Edit categories',
        'deletecat' => 'Delete categories',

        'createuser' => 'Create users',
        'edituser' => 'Edit users',
        'deleteuser' => 'Delete users',

        'creategroup' => 'Create groups',
        'editgroup' => 'Edit groups',
        'deletegroup' => 'Delete groups',

        'viewcheesto' => 'View Cheesto',
        'updatecheesto' => 'Update Cheesto',

        'admin' => 'Is Full Admin'
    ];

    public function __construct(GroupsRepo $repo)
    {
        $this->repo = $repo;
        return;
    }
    /**
     * Get group data from database
     *
     * @param int $groupID - Group ID number, if omitted returns all groups
     * @return array
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getGroupList($groupID = null)
    {
        if ($groupID === null) {
            return $this->repo->getGroupList();
        }

        if (is_numeric($groupID)) {
            $group = $this->repo->getGroupById($groupID);
        } else {
            $group = $this->repo->getGroupByName($groupID);
        }
        $group['permissions'] = unserialize($group['permissions']);
        $group['permissions'] = array_merge($this->defaultPermissions, $group['permissions']);
        $group['permissionNames'] = $this->permissionNames;
        return $group;
    }

    /**
     * Save a new group to database
     *
     * @param string $name - Name of new group
     * @param array $rights - Array containing rights
     * @return int - ID of new group
     */
    public function createGroup($name, array $rights)
    {
        $rights = array_merge($this->defaultPermissions, $rights);
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
    public function editGroup($gid, array $rights)
    {
        $rights = array_merge($this->defaultPermissions, $rights);
        $rights = serialize($rights);
        return is_numeric($this->repo->editGroup($gid, $rights));
    }

    /**
     * Get only permissions array from database for group $userrole
     *
     * @param string $userrole - Name of permisions group
     * @return array - Permissions
     */
    public function loadRights($gid)
    {
        return (array) unserialize($this->repo->loadRights($gid));
    }

    /**
     * Determine if any users belong to group id/name $group
     *
     * @param int/string $group - Group ID or name
     * @return array - Containing user IDs of users in group
     */
    public function usersExistInGroup($group)
    {
        return $this->repo->userCountInGroup($group);
    }

    public function usersInGroup($group)
    {
        return $this->repo->usersInGroup($group);
    }
}
