<?php
/**
 * Interface for administration module
 */
namespace Dandelion\Repos\Interfaces;

interface GroupsRepo
{
    public function getGroupById($gid);
    public function getGroupByName($gname);
    public function getGroupList();
    public function createGroup($name, $rights);
    public function deleteGroup($gid);
    public function editGroup($gid, $rights);
    public function loadRights($role);
    public function userCountInGroup($role);
    public function usersInGroup($role);
    public function getRightsForUser($uid);
}
