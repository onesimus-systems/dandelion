<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
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
