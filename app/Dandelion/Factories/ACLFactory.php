<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Factories;

use Dandelion\User;
use Dandelion\Repos;
use Dandelion\Auth\Keycard;

class ACLFactory
{
    public function __construct()
    {
    }

    public function createKeycard($groupID)
    {
        if (!$groupID) {
            return new Keycard();
        }

        $groupRepo = new Repos\GroupsRepo();
        $group = $groupRepo->getGroupById($groupID);

        $card = new Keycard();
        $card->loadPermissions((array) unserialize($group['permissions']));

        return $card;
    }

    public function createKeycardForUser(User $user)
    {
        return $this->createKeycard($user->get('group_id'));
    }
}
