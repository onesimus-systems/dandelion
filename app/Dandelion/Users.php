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
        $userGroup = $this->repo->getUserRole($uid);
        $userPermissions = $permissions->loadRights($userGroup);

        // TODO: Verify this actually protects against an admin deleting the
        // only admin account.
        if ($userPermissions['admin']) {
            // If the account is an admin, check all other users to make sure
            // there's at least one other user with the admin rights flag
            $otherUsers = $this->repo->getUserRole($uid, true);

            $adminFound = false;
            foreach ($otherUsers as $areTheyAdmin) {
                $userPermissions = $permissions->loadRights($areTheyAdmin['id']);

                if ($userPermissions['admin']) {
                    // If one is found, stop for loop and allow the delete
                    $adminFound = true;
                    break;
                }
            }

            if (!$adminFound) {
                return 'At least one admin account must be left to delete another admin account';
            }
        }

        // Should return 1 row
        return $this->repo->deleteUser($uid);
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
