<?php
/**
 * Cheesto presence status system
 */
namespace Dandelion;

use \Dandelion\Storage\Contracts\DatabaseConn;

class Cheesto
{
    // Order matters!! These correspond to the statusType() switch case order
    // I know, that's very bad. They should be in the database... maybe someday
    private $statusOptions = array(
        "Available",
        "Away From Desk",
        "At Lunch",
        "Out for Day",
        "Out",
        "Appointment",
        "Do Not Disturb",
        "Meeting",
        "Out Sick",
        "Vacation"
    );

    public function __construct(DatabaseConn $dbConn)
    {
        $this->dbConn = $dbConn;
        return;
    }

    /**
     * Returns JSON array of user statuses
     *
     * @return JSON - Users statuses
     */
    public function getAllStatuses()
    {
        $statuses = $this->dbConn->selectAll('presence')->get();

        foreach ($statuses as &$row) {
            $row['statusInfo'] = $this->statusType($row['status']);
        }

        $statuses['statusOptions'] = $this->getStatusText();

        return $statuses;
    }

    public function getUserStatus($uid)
    {
        $this->dbConn->select()
                     ->from(DB_PREFIX.'presence')
                     ->where('uid = :uid');
        $params = array("uid" => $uid);
        $userStatus = $this->dbConn->get($params)[0];

        $userStatus['statusInfo'] = $this->statusType($userStatus['status']);
        $userStatus['statusOptions'] = $this->getStatusText();

        return $userStatus;
    }

    public function getStatusText()
    {
        return $this->statusOptions;
    }

    /**
     * Given the status number, returns status label, symbol, and return time
     *
     * @param int $sNum - The numeric representation of a status
     *
     * @return array - 0 = Text, 1 = Symbol, 2 = Class
     */
    private function statusType($sNum)
    {
        // Eventually the front-end will be responsible for assigning colors and symbols
        $statusProps = array();

        switch($sNum) {
            case 0:
                $statusProps['symbol'] = '&#x2713;';
                $statusProps['color'] = 'green';
                break;
            case 1:
                $statusProps['symbol'] = '&#8709;';
                $statusProps['color'] = 'blue';
                break;
            case 2:
                $statusProps['symbol'] = '&#8709;';
                $statusProps['color'] = 'blue';
                break;
            case 3:
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 4:
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 5:
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 6:
                $statusProps['symbol'] = '&#x2717;&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 7:
                $statusProps['symbol'] = '&#8709;';
                $statusProps['color'] = 'blue';
                break;
            case 8:
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 9:
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            default:
                $statusProps['symbol'] = '?';
                $statusProps['color'] = 'red';
                break;
        }

        return $statusProps;
    }

    /** Updates a user's status
     *
     * @param string $message - Status message for user
     * @param int $status - Status in numerical form (see above function for number => status pairs
     * @param string $return - Date time string for return time (may also be 'Today')
     *
     * @return string
     */
    public function updateStatus($message, $status, $return, $userId)
    {
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $this->dbConn->update(DB_PREFIX.'presence')
                     ->set(array('message = :message', 'status = :setorno', 'returntime = :returntime', 'dmodified = :dmodified'))
                     ->where('uid = :uid');
        $params = array(
            'message' => $message,
            'setorno' => $status,
            'returntime' => $return,
            'dmodified' => $date,
            'uid' => $userId
        );
        
        if ($this->dbConn->go($params)) {
            return 'User status updated';
        } else {
            return 'Error updating user status';
        }
    }
}
