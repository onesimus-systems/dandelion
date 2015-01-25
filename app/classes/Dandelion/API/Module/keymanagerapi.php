<?php
/**
 *  API key management APi module
 */
namespace Dandelion\API\Module;

use \Dandelion\KeyManager;
use \Dandelion\Controllers\ApiController;

class keyManagerAPI extends BaseModule
{
    public function __construct($db, $ur, $params)
    {
        parent::__construct($db, $ur, $params);
    }

    /**
     *  Retrieve key from database for current user.
     *  If a key isn't present, create one
     *
     *  @param bool $force - Force a new key to be generated
     *
     *  @return JSON - API Key or error message
     */
    public function getKey($force = false)
    {
        $key = new KeyManager($this->db);
        return SELF::encodeKey($key->getKey($_SESSION['userInfo']['userid'], $force));
    }

    /**
     *  Called to force a new key to be generated
     */
    public function newKey()
    {
        return SELF::getKey(true);
    }

    /**
     *  Delete a user's key rendering it void
     */
    public function revokeKey()
    {
        $userid = USER_ID;

        // Check permissions
        if (isset($this->up->uid)) {
            if ($this->ur->authorized('edituser') || $this->up->uid == USER_ID) {
                $userid = $this->up->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'keyManager'));
            }
        }

        $key = new KeyManager($this->db);
        return SELF::encodeKey($key->revoke($userid));
    }

    /**
     *  Put key into JSON encoded array with 'key' as the name
     *
     *  @param string $key - API Key (or error message)
     */
    private function encodeKey($key)
    {
        return json_encode(array (
            "key" => $key
        ));
    }
}
