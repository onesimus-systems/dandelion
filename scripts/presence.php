<?php
/*
 * Lee Keitel
 * January 27, 2014
 *
 * This page handles the Cxeesto platform.
 * It is responsible for updating the status board,
 * committing status updates for users, etc.
*/

include_once 'dbconnect.php';

// Authenticate user, if fail go to login page
if (!checkLogIn()) {
    header( 'Location: index.php' );
}

/**
  * @brief Handles the display of Cxeesto statuses
  *
  * This class is used to update the Cxeesto status board.
  * It extends dbManage so an unnecassary dbManage object
  * doesn't have to be created.
  *
  * @author Lee Keitel
  * @date February 4, 2014
***/
class cxeesto extends dbManage
{
    /** Displays the labels, symbols, return times, and messages of statuses
      *
      * @param isWin - (int) Determines if calling function is from the mini or full version of Cxeesto
      * @param isWin2 - (int) Used to fix a bug with initial load of the full version
      *
      * @author Lee Keitel
      * @date February 4, 2014
    ***/
    public function checkthem($isWin, $isWin2) {
        
        // Grab users from table
        $stmt = 'SELECT * FROM presence';
        $all_users = $this->queryDB($stmt, NULL);

        // If updating for the mini version
        if ($isWin == 0 && $isWin2 == 0) {
            
            echo '<table><thead><tr><td>Name</td><td>Status</td></tr></thead><tbody>';

            foreach ($all_users as $row) {
                echo '<tr>';
                echo '<td><span title="' . $row['message'] . '" class="message">' . $row['realname'] . '</span></td>';
                
                $statusProps = $this->statusType($row['status'], ' | ', $row['return']);
                
                echo '<td class="statusi"><span title="' . $statusProps[0] . '" class="' . $statusProps[2] . '">' . $statusProps[1] . '</span></td></tr>';
            }
            echo '</tbody></table>';
            echo '<a role="button" tabindex="0" onClick="presence.popOut();" class="linklike">Popout &#264;eesto</a>';
        }
        
        // Updating the windowed version
        elseif ($isWin == 1 || $isWin2 == 1) {
            
            echo '<table><thead><tr><td>Name</td><td>Message</td><td colspan="2">Status</td><td>Last Changed</td></tr></thead><tbody>';

            foreach ($all_users as $row) {
                echo '<tr>';
                echo '<td>' . $row['realname'] . '</td><td>' . $row['message'] . '</td>';
                
                $statusProps = $this->statusType($row['status'], '<br />', $row['return']);
                
                echo '<td class="statusi"><span class="' . $statusProps[2] . '">' . $statusProps[1] . '</span></td><td>' . $statusProps[0] . '</td><td>' . $row['dmodified'] . '</td></tr>';
            }
            echo '</tbody></table>';
        }
    }

    /** Given the status number, returns status label, symbol, and return time
      *
      * @param sNum - (int) The numerical representation of a status
      * @param lBreak - (string) The type of break or other character between label and return time
      * @param returnT - (string) Return time formatted as a string
      *
      * @author Lee Keitel
      * @date February 4, 2014
    ***/
    private function statusType($sNum, $lBreak, $returnT) {
        $statusProps = array();

        switch($sNum) {
            case 1:
                $statusProps[0] = 'Available';
                $statusProps[1] = '&#x2713;';
                $statusProps[2] = 'green';
                break;
            case 2:
                $statusProps[0] = 'Away From Desk'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#8709;';
                $statusProps[2] = 'blue';
                break;
            case 3:
                $statusProps[0] = 'At Lunch'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#8709;';
                $statusProps[2] = 'blue';
                break;
            case 4:
                $statusProps[0] = 'Out For Day'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#x2717;';
                $statusProps[2] = 'red';
                break;
            case 5:
                $statusProps[0] = 'Out'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#x2717;';
                $statusProps[2] = 'red';
                break;
            case 6:
                $statusProps[0] = 'Appointment'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#x2717;';
                $statusProps[2] = 'red';
                break;
            case 7:
                $statusProps[0] = 'Do Not Disturb'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#x2717;&#x2717;';
                $statusProps[2] = 'red';
                break;
            case 8:
                $statusProps[0] = 'Meeting'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#8709;';
                $statusProps[2] = 'blue';
                break;
            case 9:
                $statusProps[0] = 'Out Sick'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#x2717;';
                $statusProps[2] = 'red';
                break;
            case 10:
                $statusProps[0] = 'Vacation'.$lBreak.'Return: '.$returnT;
                $statusProps[1] = '&#x2717;';
                $statusProps[2] = 'red';
                break;
            default:
                $statusProps[0] = 'Unknown Status'.$lBreak.'Notify Dandelion Admin';
                $statusProps[1] = '?';
                $statusProps[2] = 'red';
                break;
        }
        
        return $statusProps;
    }
}

// Variables galore!
//
// Somehow this fixes a bug where the popout looks like the mini for one cycle
// Future self, fix this. My brain hurts right now because this doesn't make sense.
//
// This is your future self, about 1 hour later. $windowed is defined in presenceWindow.php
// before this is called, so this variable is already set as 1 when the windows opens.
$windowed = isset($windowed) ? $windowed : '0';
$windowedt = isset($_POST['windowedt']) ? $_POST['windowedt'] : '0';
$setorno = isset($_POST['setorno']) ? $_POST['setorno'] : '';
$returntime = isset($_POST['returntime']) ? $_POST['returntime'] : '00:00:00';
$message = isset($_POST['message']) ? $_POST['message'] : '';
$updateCxeesto = new cxeesto;

if ($setorno == '') {
    // This means the statuses are updating on screen
    $updateCxeesto->checkthem($windowed, $windowedt);
}
else {
    // Someone wants to set their status
    $date = new DateTime();
    $date = $date->format('Y-m-d H:i:s'); // used for date modified
    
    // Connect to DB
    $conn = new dbManage;
    
    // Update the database
    $stmt = 'UPDATE `presence` SET `message` = :message, `status` = :setorno, `return` = :returntime, `dmodified` = :dmodified WHERE `uid` = :iamaRealBoy';
    $params = array(
        'message' => $message,
        'setorno' => $setorno,
        'returntime' => $returntime,
        'dmodified' => $date,
        'iamaRealBoy' => $_SESSION['userInfo']['userid'] // Don't ask
    );
    $conn->queryDB($stmt, $params);
    
    $updateCxeesto->checkthem($windowed, $windowedt); // Update status board
}

