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

interface UsersRepo
{
    public function saveUser($uid, $realname, $role, $theme, $first);
    public function createUser($username, $password, $realname, $role, $date);
    public function createUserCheesto($uid, $date);
    public function isUser($username);
    public function resetPassword($uid, $pass);
    public function deleteUser($uid);
    public function getUserRole($uid, $invert = false);
    public function getUsers($uid);
}
