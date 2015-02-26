<?php
/**
 *  API key management APi module
 */
namespace Dandelion\API\Module;

use \Dandelion\KeyManager;
use \Dandelion\Controllers\ApiController;

class KeyManagerAPI extends BaseModule
{
    /**
     *  Retrieve key from database for current user.
     *  If a key isn't present, create one
     *
     *  @param bool $force - Force a new key to be generated
     *
     *  @return JSON - API Key or error message
     */
    public function get($user = null, $force = false)
    {
        if (!$user) {
            $user = USER_ID;
        }
        $key = new KeyManager($this->repo);
        return self::encodeKey($key->getKey($user, $force));
    }

    /**
     *  Called to force a new key to be generated
     */
    public function generate()
    {
        return self::get(USER_ID, true);
    }

    /**
     *  Delete a user's key rendering it void
     */
    public function revoke()
    {
        $userid = USER_ID;

        // Check permissions
        if (isset($this->up->uid)) {
            if ($this->ur->authorized('edituser') || $this->up->uid == USER_ID) {
                $userid = $this->up->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'keymanager'));
            }
        }

        $key = new KeyManager($this->repo);
        return self::encodeKey($key->revoke($userid));
    }

    /**
     *  Put key into JSON encoded array with 'key' as the name
     *
     *  @param string $key - API Key (or error message)
     */
    private function encodeKey($key)
    {
        return ['key' => $key];
    }

    /**
     *
     */
    public function test() {
        // By time the request reaches here, the key has been verified
        return 'Key is good';
    }
}
