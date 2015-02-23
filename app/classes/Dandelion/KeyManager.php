<?php
/**
 * API key management
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\KeyManagerRepo;

class KeyManager
{
    private $repo;

    public function __construct(KeyManagerRepo $repo)
    {
        $this->repo = $repo;
        return;
    }

    public function getKey($uid, $force = false)
    {
        $key = $this->repo->getKeyForUser($uid);

        if (!empty($key) && !$force) {
            return $key['keystring'];
        }

        // Clear database of old keys for user
        $this->revoke($uid);

        // Generate new key
        $newKey = $this->generateKey(15);
        if (!$newKey) {
            return 'Error generating key.';
        }

        // Insert new key
        if ($this->repo->saveKeyForUser($uid, $newKey)) {
            return $newKey;
        } else {
            return 'Error generating key.';
        }
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
        if (!$cstrong && !$bin) {
            return '';
        }
        return bin2hex(base64_encode($bin));
    }
}
