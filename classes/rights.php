<?php
/**
  * This class allows for a central point of checking for proper authorization.
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date July 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

use Dandelion\Database\dbManage;
use Dandelion\Permissions;

class rights
{
    private $permissions = null;
    
    /**
     * Load permissions of specified user
     * 
     * @param int $userid - ID of user
     */
    public function __construct($userid) {
        $sql = 'SELECT r.permissions
                FROM '.DB_PREFIX.'rights AS r
                LEFT JOIN '.DB_PREFIX.'users AS u
                    ON u.role = r.role
                WHERE u.userid = :userid';
        $params = array(
        	'userid' => $userid
        );
        
        $conn = new dbManage();
        $rights = $conn->queryDB($sql, $params);
        $this->permissions = (array) unserialize($rights[0]['permissions']);
    }
    
    /**
     * Check if user is authorized for requested action
     * 
     * @param string $permission - Permission to check
     */
    public function authorized($permission) {
        // The admin flag grants full rights
        if ($this->permissions["admin"]) {
            return true;
        }
        
        return $this->permissions[$permission];
    }
}
