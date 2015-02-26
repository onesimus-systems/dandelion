<?php
/**
 * Interface for administration module
 */
namespace Dandelion\Repos\Interfaces;

interface RightsRepo
{
    public function getGroup($gid);
    public function getGroupList();
    public function createGroup($name, $rights);
    public function deleteGroup($gid);
    public function editGroup($gid, $rights);
    public function loadRights($role);
    public function usersInGroup($role);
    public function getRightsForUser($uid);
}
