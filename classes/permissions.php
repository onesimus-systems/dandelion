<?php
/**
 * @brief Permissions is responsible for all permissions handling in Dandelion.
 *
 * @author Lee Keitel
 * @date May 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;
use Dandelion\Database\dbManage;

class permissions
{
    private $conn;

    public function __construct($init = true)
    {
        if ($init) {
            $this->conn = new dbManage();
        }
    }

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

        return $this->conn->queryDB($stmt, $params);
    }

    public function createGroup($name, $rightsArray)
    {
        $stmt = 'INSERT INTO `'.DB_PREFIX.'rights` (`role`,`permissions`) VALUES (:role, :rights)';
        $params = array(
            'role' => strtolower($name),
            'rights' => serialize($rightsArray)
        );

        $this->conn->queryDB($stmt, $params);
        return $this->conn->lastInsertId();
    }

    public function deleteGroup($id)
    {
        $stmt = 'DELETE FROM `'.DB_PREFIX.'rights` WHERE id = :id';
        $params = array(
            'id' => $id
        );

        return $this->conn->queryDB($stmt, $params);
    }

    public function editGroup($gid, $rights)
    {
        $rights = serialize($rights);

        $stmt = 'UPDATE `'.DB_PREFIX.'rights` SET `permissions` = :newPerm WHERE `id` = :gid';
        $params = array(
            'gid' => $gid,
            'newPerm' => $rights
        );

        return $this->conn->queryDB($stmt, $params);
    }

    public function loadRights($userrole)
    {
       $stmt = 'SELECT `permissions` FROM `'.DB_PREFIX.'rights` WHERE `role` = :userrole';
       $params = array(
            'userrole' => $userrole
       );

       return unserialize($this->conn->queryDB($stmt, $params)[0]['permissions']);
    }

    public function usersInGroup($gid)
    {
        $groupName = $this->getGroupList($gid)[0]['role'];

        $stmt = 'SELECT `userid` FROM `'.DB_PREFIX.'users` WHERE `role` = :role';
        $params = array(
            'role' => $groupName
        );

        return $this->conn->queryDB($stmt, $params);
    }
}
