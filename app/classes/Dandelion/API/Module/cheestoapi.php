<?php
/**
 *  Cheesto presence system API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Cheesto;
use \Dandelion\Controllers\ApiController;

class CheestoAPI extends BaseModule
{
    /**
     *  Grab JSON array of all cheesto users and statuses
     *
     *  @return JSON
     */
    public function readAll()
    {
        if (!$this->app->config['cheestoEnabled']) {
            exit(ApiController::makeDAPI(5, 'Cheesto has been disabled.', 'cheesto'));
        }
        if (!$this->ur->authorized('viewcheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new Cheesto($this->repo);
        return $cheesto->getAllStatuses();
    }

    /**
     *  Get Cheesto status of a single user
     */
    public function read()
    {
        if (!$this->app->config['cheestoEnabled']) {
            exit(ApiController::makeDAPI(5, 'Cheesto has been disabled.', 'cheesto'));
        }
        if (!$this->ur->authorized('viewcheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new Cheesto($this->repo);
        return $cheesto->getUserStatus($this->up->uid);
    }

    /**
     *  Get the available status texts
     */
    public function statusTexts()
    {
        if (!$this->app->config['cheestoEnabled']) {
            exit(ApiController::makeDAPI(5, 'Cheesto has been disabled.', 'cheesto'));
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
            exit(ApiController::makeDAPI(5, 'Cheesto has been disabled.', 'cheesto'));
        }
        if (!$this->ur->authorized('updatecheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new Cheesto($this->repo);
        $message = $this->up->message;
        $status = $this->up->get('status', -1);
        $returntime = $this->up->get('returntime', '00:00:00');
        $userid = USER_ID;

        if ($this->up->uid) { // A status of another user is trying to be updated
            if ($this->ur->authorized('edituser') || $this->up->uid == USER_ID) {
                $userid = $this->up->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
            }
        }

        return $cheesto->updateStatus($message, $status, $returntime, $userid);
    }
}
