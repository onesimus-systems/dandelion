<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Repos;

use Dandelion\Repos\Interfaces;

class GroupsRepo extends BaseRepo implements Interfaces\GroupsRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'group';
    }

    private function fixGroupFieldTypes(&$record)
    {
        if (is_null($record)) { return; }
        $record['id'] = (int) $record['id'];
    }

    public function getGroupById($gid)
    {
        $group = $this->database
            ->readItem($this->table, $gid);
        $this->fixGroupFieldTypes($group);
        return $group;
    }

    public function getGroupByName($gname)
    {
        $group =  $this->database
            ->find($this->table)
            ->whereEqual('name', $gname)
            ->readRecord();
        $this->fixGroupFieldTypes($group);
        return $group;
    }

    public function getGroupList()
    {
        $groups = $this->database
            ->find($this->table)->read('id, name');

        foreach ($groups as $group) {
            $this->fixGroupFieldTypes($group);
        }

        return $groups;
    }

    public function createGroup($name, $rights)
    {
        return $this->database
            ->createItem($this->table, ['name' => $name, 'permissions' => $rights]);
    }

    /**
     * Remove group from database
     *
     * @param int $id - ID number of group
     * @return bool - Status of query
     */
    public function deleteGroup($gid)
    {
        return $this->database
            ->deleteItem($this->table, $gid);
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
        return $this->database
            ->updateItem($this->table, $gid, ['permissions' => $rights]);
    }

    /**
     * Get only permissions array from database for group $userrole
     *
     * @param string $userrole - Name of permisions group
     * @return array - Permissions
     */
    public function loadRights($gid)
    {
        $record = $this->database
            ->find($this->table)
            ->whereEqual('id', $gid)
            ->readField('permissions');
        return $record;
    }

    /**
     * Determine if any users belong to group id $gid
     *
     * @param int $gid - Group ID number
     * @return int - Count of users in group
     */
    public function userCountInGroup($gid)
    {
        return (int) $this->database
            ->find($this->prefix.'user')
            ->whereEqual('group_id', $gid)
            ->count();
    }

    /**
     * Get usernames of users in a group
     *
     * @param  string $gid Group name
     * @return array        Usernames in group
     */
    public function usersInGroup($gid)
    {
        return $this->database->find($this->prefix.'user')
            ->whereEqual('group_id', $gid)
            ->read('username, fullname');
    }

    public function getRightsForUser($uid)
    {
        $record = $this->database->find($this->table)
            ->has($this->prefix.'user', 'group_id')
            ->whereEqual($this->prefix.'user.id', $uid)
            ->readRecord();

        return $record['permissions'];
    }
}
