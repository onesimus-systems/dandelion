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

use Dandelion\Repos\Interfaces\UsersRepo;

class Users
{
    protected $repo;

    public function __construct(UsersRepo $repo)
    {
        $app = Application::getInstance();
        list(, $caller) = debug_backtrace(false, 2);
        // Log deprication notice
        $app->logger->warning('Users class is depricated. Use the User object or UserFactory instead : {function}', [
            'function' => $caller['class'].'::'.$caller['function']
        ]);

        $this->repo = $repo;
    }

    public function deleteUser($uid, Groups $permissions)
    {
        $delete = false;
        $userGroup = $this->repo->getUserRole($uid);
        $isAdmin = $permissions->loadRights($userGroup);

        if (!$isAdmin['admin']) {
            // If the account being deleted isn't an admin, then there's nothing to worry about
            $delete = true;
        } else {
            // If the account IS an admin, check all other users to make sure
            // there's at least one other user with the admin rights flag
            $otherUsers = $this->repo->getUserRole($uid, true);

            foreach ($otherUsers as $areTheyAdmin) {
                $isAdmin = $permissions->loadRights($areTheyAdmin['id']);

                if ($isAdmin['admin']) {
                    // If one is found, stop for loop and allow the delete
                    $delete = true;
                    break;
                }
            }
        }

        if ($delete) {
            // Should return 1 row
            return $this->repo->deleteUser($uid);
        } else {
            return 'At least one admin account must be left to delete another admin account';
        }
    }

    public function getUserList()
    {
        return $this->repo->getUsers();
    }

    public function getUser($uid)
    {
        return $this->repo->getUsers($uid)[0];
    }
}
