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
     * @param string variable length - Permissions to check
     */
    public function authorized()
    {
        if (!func_num_args()) {
            return false;
        }

        $permissions = func_get_args();
        if (is_array($permissions[0])) {
            // For legacy support, the old function definition
            // called for an array as the only parameter. This fixes
            // the expected array to be the array provided
            $permissions = $permissions[0];
        }

        // The admin flag grants full rights
        if ($this->permissions['admin']) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (isset($this->permissions[strtolower($permission)])
                && $this->permissions[strtolower($permission)]) {
                return true;
            }
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
