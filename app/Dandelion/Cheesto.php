<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion;

use Dandelion\Utils\Configuration as Config;
use Dandelion\Repos\Interfaces\CheestoRepo;

class Cheesto
{
    protected $repo;
    protected $statusOptions = [];

    public function __construct(CheestoRepo $repo)
    {
        $this->statusOptions = Config::get('cheesto', ['statusOptions' => []])['statusOptions'];
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

    /**
     * Updates a user's status
     *
     * @param string $message - Status message for user
     * @param string $status - Status text
     * @param string $return - Date time string for return time (may also be 'Today')
     * @param int    $uid    - User ID
     *
     * @return string
     */
    public function updateStatus($message, $status, $return, $uid)
    {
        return $this->repo->updateStatus($uid, $status, $message, $return, date('Y-m-d H:i:s'));
    }
}
