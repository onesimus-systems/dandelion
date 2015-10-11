<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\Module;

use Dandelion\KeyManager;
use Dandelion\Controllers\ApiController;
use Dandelion\Exception\ApiPermissionException;

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
        $user = $user ?: $this->requestUser->get('id');
        $key = new KeyManager($this->repo);
        return $key->getKey($user, $force);
    }

    /**
     *  Called to force a new key to be generated
     */
    public function generate()
    {
        return self::get($this->requestUser->get('id'), true);
    }

    /**
     *  Delete a user's key rendering it void
     */
    public function revoke()
    {
        $userid = $this->requestUser->get('id');
        $requestedUid = $this->request->postParam('uid');

        // Check permissions
        if ($requestedUid) {
            if ($this->authorized($this->requestUser, 'edit_user') || $requestedUid == $userid) {
                $userid = $requestedUid;
            } else {
                throw new ApiPermissionException();
            }
        }

        $key = new KeyManager($this->repo);

        if (is_numeric($key->revoke($userid))) {
            return 'Key revoked successfully';
        } else {
            throw new ApiException('Error revoking key', 5);
        }
    }

    /**
     * Test an API key for validity
     */
    public function test() {
        // By time the request reaches here, the key has been verified
        return true;
    }
}
