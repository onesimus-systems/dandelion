<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\v1;

use Dandelion\KeyManager;
use Dandelion\Controllers\ApiController;
use Dandelion\Exception\ApiPermissionException;
use Dandelion\API\ApiCommander;
use Dandelion\API\BaseModule;

class KeyManagerAPI extends BaseModule
{
    // Public facing methods can only accept a parameter of the url parameters.
    // Get doesn't take any parameters they would interfere if the internal
    // get method was set as the public one. Thus, this is simply a wrapper.
    public function get()
    {
        return $this->getInternal($this->requestUser->get('id'), false);
    }

    /**
     *  Retrieve key from database for current user.
     *  If a key isn't present, create one
     *
     *  @param int  $user Id of user to get api key
     *  @param bool $force Force a new key to be generated
     *
     *  @return JSON - API Key or error message
     */
    protected function getInternal($user = null, $force = false)
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
        return $this->getInternal($this->requestUser->get('id'), true);
    }

    /**
     *  Delete a user's key rendering it void
     */
    public function revoke($params)
    {
        $userid = $this->requestUser->get('id');
        $requestedUid = $params->uid;

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
            throw new ApiException('Error revoking key', ApiCommander::API_GENERAL_ERROR);
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
