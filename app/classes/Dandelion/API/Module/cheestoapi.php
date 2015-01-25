<?php
/**
 *  Cheesto presence system API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Cheesto;
use \Dandelion\Controllers\ApiController;

class cheestoAPI extends BaseModule
{
    public function __construct($db, $ur, $params)
    {
        parent::__construct($db, $ur, $params);
    }

    /**
     *  Grab JSON array of all cheesto users and statuses
     *
     *  @return JSON
     */
    public function readAll()
    {
        if (!$this->ur->authorized('viewcheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new Cheesto($this->db);
        return json_encode($cheesto->getAllStatuses());
    }

    /**
     *  Get Cheesto status of a single user
     */
    public function read()
    {
        if (!$this->ur->authorized('viewcheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new Cheesto($this->db);
        return json_encode($cheesto->getUserStatus($this->up->uid));
    }

    /**
     *  Get the available status texts
     */
    public function statusTexts()
    {
        $cheesto = new Cheesto($this->db);
        return json_encode($cheesto->getStatusText());
    }

    /**
     *  Update the status of user
     *
     *  @return JSON
     */
    public function update()
    {
        if (!$this->ur->authorized('updatecheesto')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
        }

        $cheesto = new Cheesto($this->db);
        $message = $this->up->message;
        $status = $this->up->get('status', -1);
        $returntime = $this->up->get('returntime', '00:00:00');
        $userid = USER_ID;

        if (isset($this->up->uid)) { // A status of another user is trying to be updated
            if ($this->ur->authorized('edituser') || $this->up->uid == USER_ID) {
                $userid = $this->up->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'cheesto'));
            }
        }

        return $cheesto->updateStatus($message, $status, $returntime, $userid);
    }
}
