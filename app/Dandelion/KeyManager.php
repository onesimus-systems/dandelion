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

use Dandelion\Repos\Interfaces\KeyManagerRepo;

class KeyManager
{
    private $repo;

    public function __construct(KeyManagerRepo $repo)
    {
        $this->repo = $repo;
        return;
    }

    /**
     * getKey will return the current API key for the user identified by $uid.
     * If the user doesn't have a key, one will be generated.
     *
     * @param int $uid - User's ID number
     */
    public function getKey($uid)
    {
        $key = $this->repo->getKeyForUser($uid);
        if ($key) {
            return $key['keystring'];
        }

        // Generate new key
        $newKey = $this->generateKey(15);
        if (!$newKey) {
            return 'Error generating key.';
        }

        // Insert new key
        if ($this->repo->saveKeyForUser($uid, $newKey)) {
            return $newKey;
        }
        return 'Error generating key.';
    }

    public function revoke($uid)
    {
        return $this->repo->revoke($uid);
    }

    /**
     * Generate a random alphanumeric string
     *
     * @param int $length - Length of generated string
     *
     * @return string
     */
    private function generateKey($length = 10)
    {
        $bin = openssl_random_pseudo_bytes($length, $cstrong);
        if (!$cstrong || !$bin) {
            return '';
        }
        return bin2hex($bin);
    }
}
