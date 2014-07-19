<?php
/**
 * This class is used to update the Cxeesto status board.
 * It extends dbManage so an unnecessary dbManage object
 * doesn't have to be created.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date Feb 2014
 ***/
namespace Dandelion;

class cxeesto extends Database\dbManage
{
    // Order matters!! These correspond to the statusType() switch case order
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
    /**
     * Returns JSON array of user statuses
     * 
     * @return JSON - Users statuses
     */
    public function getJson() {
        $statuses = $this->selectAll('presence');
        
        foreach ($statuses as &$row) {
            $row['statusInfo'] = $this->statusType($row['status'], '&#013;', $row['returntime']);
        }
        
        $statuses['statusOptions'] = $this->statusOptions;
        
        return json_encode($statuses);
    }

    /**
     * Given the status number, returns status label, symbol, and return time
     *
     * @param int $sNum - The numeric representation of a status
     * @param string $lBreak - The type of break or other character between label and return time
     * @param string $returnT - Return time formatted as string
     *
     * @return array - 0 = Text, 1 = Symbol, 2 = Class
     */
    private function statusType($sNum, $lBreak, $returnT)
    {
        $statusProps = array();

        switch($sNum) {
            case 1:
                $statusProps['status'] = 'Available';
                $statusProps['symbol'] = '&#x2713;';
                $statusProps['color'] = 'green';
                break;
            case 2:
                $statusProps['status'] = 'Away From Desk'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#8709;';
                $statusProps['color'] = 'blue';
                break;
            case 3:
                $statusProps['status'] = 'At Lunch'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#8709;';
                $statusProps['color'] = 'blue';
                break;
            case 4:
                $statusProps['status'] = 'Out For Day'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 5:
                $statusProps['status'] = 'Out'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 6:
                $statusProps['status'] = 'Appointment'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 7:
                $statusProps['status'] = 'Do Not Disturb'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#x2717;&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 8:
                $statusProps['status'] = 'Meeting'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#8709;';
                $statusProps['color'] = 'blue';
                break;
            case 9:
                $statusProps['status'] = 'Out Sick'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            case 10:
                $statusProps['status'] = 'Vacation'.$lBreak.'Return: '.$returnT;
                $statusProps['symbol'] = '&#x2717;';
                $statusProps['color'] = 'red';
                break;
            default:
                $statusProps['status'] = 'Unknown Status'.$lBreak.'Notify Dandelion Admin';
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

        $stmt = 'UPDATE `'.DB_PREFIX.'presence` SET `message` = :message, `status` = :setorno, `returntime` = :returntime, `dmodified` = :dmodified WHERE `uid` = :uid';
        $params = array(
            'message' => urldecode($message),
            'setorno' => $status,
            'returntime' => $return,
            'dmodified' => $date,
            'uid' => $userId
        );

        $this->queryDB($stmt, $params);

        return json_encode('User status updated');
    }
}
