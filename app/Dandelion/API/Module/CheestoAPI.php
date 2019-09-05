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
use Dandelion\API\ApiCommander;

class CheestoAPI extends BaseModule
{
    /**
     *  Grab JSON array of all cheesto users and statuses
     *
     *  @return JSON
     */
    public function read($params)
    {
        if (!Config::get('cheestoEnabled')) {
            throw new ApiException('Cheesto has been disabled', ApiCommander::API_CHEESTO_DISABLED);
        }
        if (!$this->authorized($this->requestUser, 'view_cheesto')) {
            throw new ApiPermissionException();
        }

        $cheesto = new Cheesto($this->repo);
        return [
            'statuses' => $cheesto->getUserStatus($params->uid),
            'statusOptions' => $cheesto->getStatusText(),
        ];
    }

    /**
     *  Get the available status texts
     */
    public function statusTexts()
    {
        if (!Config::get('cheestoEnabled')) {
            throw new ApiException('Cheesto has been disabled', ApiCommander::API_CHEESTO_DISABLED);
        }
        $cheesto = new Cheesto($this->repo);
        return $cheesto->getStatusText();
    }

    /**
     *  Update the status of user
     *
     *  @return string
     */
    public function update($params)
    {
        if (!Config::get('cheestoEnabled')) {
            throw new ApiException('Cheesto has been disabled', ApiCommander::API_CHEESTO_DISABLED);
        }
        if (!$this->authorized($this->requestUser, 'update_cheesto')) {
            throw new ApiPermissionException();
        }

        $cheesto = new Cheesto($this->repo);
        $userid = $this->requestUser->get('id');

        if ($params->uid) { // A status of another user is trying to be updated
            if ($params->uid != $userid && !$this->authorized($this->requestUser, 'edit_user')) {
                throw new ApiPermissionException();
            }
            $userid = $params->uid;
        }

        if ($cheesto->updateStatus(
            htmlspecialchars($params->message),
            htmlspecialchars($params->status),
            $params->returntime,
            $userid)) {
            return 'Status updated successfully';
        }
        throw new ApiException('Error updating status', ApiCommander::API_GENERAL_ERROR);
    }
}
