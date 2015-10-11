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

use Dandelion\Auth\Keycard;
use Dandelion\Repos\Interfaces\UserRepo;
use Dandelion\Repos\Interfaces\CheestoRepo;

class User
{
    private $repo;
    private $cheestoRepo;
    private $keycard;
    private $valid = true;

    // User data
    private $userInfo = [];

    public function __construct(UserRepo $repo, CheestoRepo $cheestoRepo, $uid = 0, $username = '')
    {
        $this->repo = $repo;
        $this->cheestoRepo = $cheestoRepo;
        $this->userInfo['id'] = $uid;
        $this->userInfo['username'] = $username;
        $this->load();
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function get($key)
    {
        if (is_array($key)) {
            $returned = [];

            foreach ($key as $value) {
                $returned[$value] = $this->get($value);
            }

            return $returned;
        }

        if (!isset($this->userInfo[$key])) {
            return null;
        }

        return $this->userInfo[$key];
    }

    public function set($key, $value)
    {
        $this->userInfo[$key] = $value;
    }

    public function load(array $data = null)
    {
        if (!is_null($data)) {
            $this->userInfo = $data;
            return true;
        }

        if ($this->get('id') > 0) {
            $this->userInfo = $this->repo->getUserById($this->get('id'));
        } elseif ($this->get('username') !== '') {
            $this->userInfo = $this->repo->getUserByName($this->get('username'));
        }

        $this->valid = !(empty($this->userInfo));
    }

    public function save()
    {
        if ($this->get('id') === 0) {
            return $this->create();
        } else {
            return $this->update();
        }
    }

    public function setPassword($pass = '')
    {
        $this->set('password', $this->doHash($pass));
    }

    public function delete()
    {
        return is_numeric($this->repo->deleteUser($this->get('id')));
    }

    public function enable()
    {
        $this->set('disabled', 0);
    }

    public function disable()
    {
        $this->set('disabled', 1);
    }

    public function enabled()
    {
        return !((bool) $this->get('disabled'));
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function giveKeycard(Keycard $card)
    {
        $this->keycard = $card;
    }

    public function getKeycard()
    {
        return $this->keycard;
    }

    private function update()
    {
        if ($this->get('disabled') === null) {
            $this->enable();
        }

        // Update main user row
        $userSaved = $this->repo->saveUser(
            $this->get('id'),
            $this->get('fullname'),
            $this->get('group_id'),
            $this->get('theme'),
            $this->get('initial_login'),
            $this->get('disabled'),
            $this->get('password')
        );

        return is_numeric($userSaved);
    }

    private function create()
    {
        $date = new \DateTime();

        // Create row in users table
        $userCreated = $this->repo->createUser(
            $this->get('username'),
            $this->get('password'),
            $this->get('fullname'),
            $this->get('group_id'),
            $date->format('Y-m-d')
        );

        $userCheestoCreated = true;
        $userCheestoCreated = $this->cheestoRepo->createCheesto(
            $userCreated,
            $date->format('Y-m-d H:i:s')
        );

        return (is_numeric($userCreated) && is_numeric($userCheestoCreated));
    }

    private function doHash($s)
    {
        return password_hash($s, PASSWORD_BCRYPT);
    }
}
