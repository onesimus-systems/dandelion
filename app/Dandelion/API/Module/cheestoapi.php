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

use \Dandelion\Cheesto;
use \Dandelion\Exception\ApiException;
use \Dandelion\Controllers\ApiController;
use \Dandelion\Exception\ApiPermissionException;

class CheestoAPI extends BaseModule
{
    /**
     *  Grab JSON array of all cheesto users and statuses
     *
     *  @return JSON
     */
    public function read()
    {
        if (!$this->app->config['cheestoEnabled']) {
            throw new ApiException('Cheesto has been disabled', 5);
        }
        if (!$this->ur->authorized('viewcheesto')) {
            throw new ApiPermissionException();
        }

        $cheesto = new Cheesto($this->repo);

        if ($this->up->uid) {
            return $cheesto->getUserStatus($this->up->uid);
        } else {
            return $cheesto->getUserStatus();
        }
    }

    /**
     *  Get the available status texts
     */
    public function statusTexts()
    {
        if (!$this->app->config['cheestoEnabled']) {
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
        if (!$this->app->config['cheestoEnabled']) {
            throw new ApiException('Cheesto has been disabled', 5);
        }
        if (!$this->ur->authorized('updatecheesto')) {
            throw new ApiPermissionException();
        }

        $cheesto = new Cheesto($this->repo);
        $message = $this->up->get('message', '');
        $status = $this->up->get('status', 'Available');
        $returntime = $this->up->get('returntime', '00:00:00');
        $userid = USER_ID;

        if ($this->up->uid) { // A status of another user is trying to be updated
            if ($this->up->uid == USER_ID || $this->ur->authorized('edituser')) {
                $userid = $this->up->uid;
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
