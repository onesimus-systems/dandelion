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

use Dandelion\Cheesto;
use Dandelion\Exception\ApiException;
use Dandelion\Controllers\ApiController;
use Dandelion\Exception\ApiPermissionException;
use Dandelion\Utils\Configuration as Config;

class CheestoAPI extends BaseModule
{
    /**
     *  Grab JSON array of all cheesto users and statuses
     *
     *  @return JSON
     */
    public function read()
    {
        if (!Config::get('cheestoEnabled')) {
            throw new ApiException('Cheesto has been disabled', 5);
        }
        if (!$this->authorized($this->requestUser, 'view_cheesto')) {
            throw new ApiPermissionException();
        }

        $cheesto = new Cheesto($this->repo);
        return $cheesto->getUserStatus($this->request->getParam('uid'));
    }

    /**
     *  Get the available status texts
     */
    public function statusTexts()
    {
        if (!Config::get('cheestoEnabled')) {
            throw new ApiException('Cheesto has been disabled', 5);
        }
        $cheesto = new Cheesto($this->repo);
        return $cheesto->getStatusText();
    }

    /**
     *  Update the status of user
     *
     *  @return JSON
     */
    public function update()
    {
        if (!Config::get('cheestoEnabled')) {
            throw new ApiException('Cheesto has been disabled', 5);
        }
        if (!$this->authorized($this->requestUser, 'update_cheesto')) {
            throw new ApiPermissionException();
        }

        $cheesto = new Cheesto($this->repo);
        $message = $this->request->postParam('message', '');
        $status = $this->request->postParam('status', 'Available');
        $returntime = $this->request->postParam('returntime', '00:00:00');
        $userid = $this->requestUser->get('id');
        $requestedUid = $this->request->postParam('uid');

        if ($requestedUid) { // A status of another user is trying to be updated
            if ($requestedUid == $userid || $this->authorized($this->requestUser, 'edit_user')) {
                $userid = $requestedUid;
            } else {
                throw new ApiPermissionException();
            }
        }

        if ($cheesto->updateStatus($message, $status, $returntime, $userid)) {
            return 'Status updated successfully';
        } else {
            throw new ApiException('Error updating status', 5);
        }
    }
}
