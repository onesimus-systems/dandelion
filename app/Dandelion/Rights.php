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

use \Dandelion\Repos\Interfaces\GroupsRepo;

class Rights
{
    private $permissions = null;

    /**
     * Load permissions of specified user
     *
     * @param int $userid - ID of user
     */
    public function __construct($userid, GroupsRepo $repo)
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
