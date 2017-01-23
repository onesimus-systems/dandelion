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

interface UserRepo
{
    public function saveUser($uid, $realname, $role, $theme, $first, $disabled, $apiOverride, $password = null);
    public function createUser($username, $password, $fullname, $role, $date, $prompt, $apiOverride);
    public function deleteUser($uid);
    public function getUserById($uid);
    public function getUserByName($username);
}
