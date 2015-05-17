<?php
/**
 * Created by PhpStorm.
 * User: lfkeitel
 * Date: 1/31/15
 * Time: 12:17 PM
 */
namespace Dandelion\Repos\Interfaces;

interface UsersRepo
{
    public function saveUser($uid, $realname, $role, $theme, $first);
    public function saveUserCheesto($uid, $realname);
    public function createUser($username, $password, $realname, $role, $date);
    public function createUserCheesto($uid, $realname, $date);
    public function isUser($username);
    public function resetPassword($uid, $pass);
    public function deleteUser($uid);
    public function getUserRole($uid, $invert = false);
    public function getUsers($uid);
}
