<?php
/**
 * Rights group management
 */
namespace Dandelion;

use \Dandelion\Storage\Contracts\DatabaseConn;

class Permissions
{
    public function __construct(DatabaseConn $dbConn)
    {
        $this->dbConn = $dbConn;
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
            $this->dbConn->selectAll('rights');
            $params = [];
        } else {
            $this->dbConn->select()
                         ->from(DB_PREFIX.'rights')
                         ->where(array('id = :id'));
            $params = array(
                'id' => $groupID
            );
        }
        return $this->dbConn->get($params);
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
        $this->dbConn->insert()
                     ->into(DB_PREFIX.'rights', array('role', 'permissions'))
                     ->values(array(':role', ':rights'));
        $params = array(
            'role' => strtolower($name),
            'rights' => serialize($rightsArray)
        );
        $this->dbConn->go($params);
        return $this->dbConn->lastInsertId();
    }

    /**
     * Remove group from database
     *
     * @param int $id - ID number of group
     * @return bool - Status of query
     */
    public function deleteGroup($id)
    {
        $this->dbConn->delete()
                     ->from(DB_PREFIX.'rights')
                     ->where(array('id = :id'));
        $params = array(
            'id' => $id
        );
        return $this->dbConn->go($params);
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
        $this->dbConn->update(DB_PREFIX.'rights')
                     ->set(array('permissions = :newPerm'))
                     ->where(array('id = :gid'));
        $params = array(
            'gid' => $gid,
            'newPerm' => $rights
        );
        return $this->dbConn->go($params);
    }

    /**
     * Get only permissions array from database for group $userrole
     *
     * @param string $userrole - Name of permisions group
     * @return array - Permissions
     */
    public function loadRights($userrole)
    {
       $this->dbConn->select('permissions')
                    ->from(DB_PREFIX.'rights')
                    ->where(array('role = :userrole'));
       $params = array(
            'userrole' => $userrole
       );
       return unserialize($this->dbConn->getFirst($params)['permissions']);
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
        $this->dbConn->select('userid')
                     ->from(DB_PREFIX.'users')
                     ->where(array('role = :role'));
        $params = array(
            'role' => $groupName
        );
        return $this->dbConn->get($params);
    }
}
