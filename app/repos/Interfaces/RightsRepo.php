<?php
/**
 * Interface for administration module
 */
namespace Dandelion\Repos\Interfaces;

interface RightsRepo
{
    public function getGroupList($gid);
    public function getAllGroupLists();
    public function createGroup($name, $rights);
    public function deleteGroup($gid);
    public function editGroup($gid, $rights);
    public function loadRights($role);
    public function usersInGroup($role);
}
