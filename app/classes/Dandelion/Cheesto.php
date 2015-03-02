<?php
/**
 * Cheesto presence status system
 */
namespace Dandelion;

use \Dandelion\Utils\Configuration;
use \Dandelion\Repos\Interfaces\CheestoRepo;

class Cheesto
{
    public function __construct(CheestoRepo $repo)
    {
        $this->statucOptions = Configuration::getConfig()['cheesto']['statusOptions'];
        $this->repo = $repo;
        return;
    }

    public function getUserStatus($uid = null)
    {
        if ($uid) {
            $statuses = $this->repo->getUserStatus($uid);
        } else {
            $statuses = $this->repo->getAllStatuses();
        }
        $statuses['statusOptions'] = $this->getStatusText();
        return $statuses;
    }

    public function getStatusText()
    {
        return $this->statusOptions;
    }

    /** Updates a user's status
     *
     * @param string $message - Status message for user
     * @param string $status - Status text
     * @param string $return - Date time string for return time (may also be 'Today')
     *
     * @return string
     */
    public function updateStatus($message, $status, $return, $uid)
    {
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        if ($this->repo->updateStatus($uid, $status, $message, $return, $date)) {
            return 'User status updated';
        } else {
            return 'Error updating user status';
        }
    }
}
