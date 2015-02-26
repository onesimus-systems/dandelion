<?php
/**
 * MySQL repository for administration module
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class RightsRepo extends BaseMySqlRepo implements Interfaces\RightsRepo
{
    public function getGroup($gid)
    {
        $this->database->select()
            ->from($this->prefix . 'rights')
            ->where(['id = :id']);

        return $this->database->getFirst(['id' => $gid]);
    }

    public function getGroupList()
    {
        return $this->database->select('id, role')
            ->from($this->prefix . 'rights')
            ->get();
    }

    public function createGroup($name, $rights)
    {
        $this->database->insert()
            ->into($this->prefix . 'rights', ['role', 'permissions'])
            ->values([':role', ':rights']);

        $this->database->go(['role' => $name, 'rights' => $rights]);
        return $this->database->lastInsertId();
    }

    /**
     * Remove group from database
     *
     * @param int $id - ID number of group
     * @return bool - Status of query
     */
    public function deleteGroup($gid)
    {
        $this->database->delete()
            ->from($this->prefix . 'rights')
            ->where(['id = :id']);

        return $this->database->go(['id' => $gid]);
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
        $this->database->update($this->prefix . 'rights')
            ->set(['permissions = :rights'])
            ->where(['id = :gid']);

        return $this->database->go(['gid' => $gid, 'rights' => $rights]);
    }

    /**
     * Get only permissions array from database for group $userrole
     *
     * @param string $userrole - Name of permisions group
     * @return array - Permissions
     */
    public function loadRights($role)
    {
        $this->database->select('permissions')
            ->from($this->prefix . 'rights')
            ->where(['role = :role']);

        return $this->database->getFirst(['role' => $role])['permissions'];
    }

    /**
     * Determine if any users belong to group id $gid
     *
     * @param int $gid - Group ID number
     * @return array - Containing user IDs of users in group
     */
    public function usersInGroup($role)
    {
        $this->database->select('userid')
            ->from($this->prefix . 'users')
            ->where(['role = :role']);

        return $this->database->get(['role' => $role]);
    }

    public function getRightsForUser($uid)
    {
        $this->database->select('r.permissions')
            ->from($this->prefix . 'rights AS r
              LEFT JOIN ' . $this->prefix . 'users AS u
                  ON u.role = r.role')
            ->where('u.userid = :uid');

        return $this->database->getFirst(['uid' => $uid])['permissions'];
    }
}
