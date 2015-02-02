<?php
/**
 * Encapsulation of a user's permissions
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\RightsRepo;

class Rights
{
    private $permissions = null;

    /**
     * Load permissions of specified user
     *
     * @param int $userid - ID of user
     */
    public function __construct($userid, RightsRepo $repo)
    {
        $this->userid = $userid;
        $this->permissions = (array)unserialize($repo->getRightsForUser($userid));
    }

    /**
     * Check if user is authorized for requested action
     *
     * @param string $permission - Permission to check
     */
    public function authorized($permissions)
    {
        // The admin flag grants full rights
        if ($this->permissions['admin']) {
            return true;
        }

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ($this->permissions[strtolower($permission)]) {
                    return true;
                }
            }
        } else {
            return $this->permissions[strtolower($permissions)];
        }

        return false;
    }

    public function isAdmin()
    {
        return $this->permissions['admin'];
    }

    public function getRightsForUser()
    {
        return $this->permissions;
    }
}
