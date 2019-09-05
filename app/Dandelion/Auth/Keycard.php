<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Auth;

use Dandelion\Repos\Interfaces\GroupsRepo;

class Keycard
{
    private $permissions = [];

    public function __construct()
    {
    }

    public function loadPermissions(array $permissions)
    {
        $this->permissions = $permissions;
    }

    public function read($permission)
    {
        return array_key_exists($permission, $this->permissions) ? $this->permissions[$permission] : false;
    }

    public function readAll()
    {
        return $this->permissions;
    }

    public function set($permission, $value)
    {
        $this->permissions[$permission] = $value;
    }
}
