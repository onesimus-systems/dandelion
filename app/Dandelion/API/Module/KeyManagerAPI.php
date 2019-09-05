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
use Dandelion\Exception\{ApiPermissionException, ApiException};
use Dandelion\API\ApiCommander;

class KeyManagerAPI extends BaseModule
{
    // Public facing methods can only accept a parameter of the url parameters.
    // Get doesn't take any parameters they would interfere if the internal
    // get method was set as the public one. Thus, this is simply a wrapper.
    public function get()
    {
        $user = $this->requestUser->get('id');
        $key = new KeyManager($this->repo);
        return $key->getKey($user);
    }

    /**
     *  Called to force a new key to be generated
     */
    public function generate()
    {
        $user = $this->requestUser->get('id');
        $key = new KeyManager($this->repo);
        if (!$key->revoke($user)) {
            throw new ApiException('Error revoking API key');
        }
        return $key->getKey($user);
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
            if ($requestedUid != $userid && !$this->authorized($this->requestUser, 'edit_user')) {
                throw new ApiPermissionException();
            }
            $userid = $requestedUid;
        }

        $key = new KeyManager($this->repo);

        if (is_numeric($key->revoke($userid))) {
            return 'Key revoked successfully';
        }
        throw new ApiException('Error revoking key', ApiCommander::API_GENERAL_ERROR);
    }

    /**
     * Test an API key for validity
     */
    public function test() {
        // By time the request reaches here, the key has been verified
        return true;
    }
}
