<?php
include 'permguest.php';

$windowed = isset($windowed) ? $windowed : '0';
$windowedt = isset($_POST['windowedt']) ? vali($_POST['windowedt']) : '0';
$setorno = isset($_POST['setorno']) ? vali($_POST['setorno']) : '';
$returntime = isset($_POST['returntime']) ? vali($_POST['returntime']) : '00:00:00';
$message = isset($_POST['message']) ? vali($_POST['message']) : '';

if ($setorno == '') {
    checkthem($con, $windowed, $windowedt);
}
else {
    $date = new DateTime();
    $date = $date->format('Y-m-d H:i:s');
    
    if (!mysqli_query($con, 'UPDATE `presence` SET `message` = "'.$message . '", `status` = "'.$setorno.'", `return` = "'.$returntime.'", `dmodified` = "'.$date.'" WHERE `realname` = "'.$realname.'"')){
        die('Error setting status: ' . mysqli_error($con));
    }
    
    checkthem($con, $windowed, $windowedt);
}

function checkthem($con, $isWin, $isWin2) {
    $grab_logs = mysqli_query($con, "SELECT * FROM presence");

    if ($isWin == 0 && $isWin2 == 0) {
        
        echo '<table><thead><tr><td>Name</td><td>Status</td></tr></thead><tbody>';

        while ($row = mysqli_fetch_array($grab_logs)) {
            echo '<tr>';
            echo '<td><span title="' . $row['message'] . '" class="message">' . $row['realname'] . '</span></td>';
            
            switch($row['status']) {
                case 1:
                    $statusText = "Available";
                    $statusSym = "&#x2713;";
                    $statusClass = "green";
                    break;
                case 2:
                    $statusText = "Away From Desk\nReturn: ".$row['return'];
                    $statusSym = "&#8709;";
                    $statusClass = "blue";
                    break;
                case 3:
                    $statusText = "At Lunch\nReturn: ".$row['return'];
                    $statusSym = "&#8709;";
                    $statusClass = "blue";
                    break;
                case 4:
                    $statusText = "Out For Day\nReturn: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                case 5:
                    $statusText = "Out\nReturn: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                case 6:
                    $statusText = "Appointment\nReturn: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                case 7:
                    $statusText = "Do Not Disturb\nReturn: ".$row['return'];
                    $statusSym = "&#x2717;&#x2717;";
                    $statusClass = "red";
                    break;
                case 8:
                    $statusText = "Meeting\nReturn: ".$row['return'];
                    $statusSym = "&#8709;";
                    $statusClass = "blue";
                    break;
                case 9:
                    $statusText = "Out Sick\nReturn: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                case 10:
                    $statusText = "Vacation\nReturn: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                default:
                    $statusText = "Unknown Status\nNotify Dandelion Admin";
                    $statusSym = "?";
                    $statusClass = "red";
                    break;
            }
            
            echo '<td class="statusi"><span title="' . $statusText . '" class="' . $statusClass . '">' . $statusSym . '</span></td></tr>';
        }
        echo '</tbody></table>';
        echo '<a role="button" tabindex="0" onClick="presence.popOut();" class="linklike">Popout &#264;eesto</a>';
    }
    elseif ($isWin == 1 || $isWin2 == 1) {
        
        echo '<table><thead><tr><td>Name</td><td>Message</td><td colspan="2">Status</td><td>Last Changed</td></tr></thead><tbody>';

        while ($row = mysqli_fetch_array($grab_logs)) {
            echo '<tr>';
            echo '<td>' . $row['realname'] . '</td><td>' . $row['message'] . '</td>';
            
            switch($row['status']) {
                case 1:
                    $statusText = "Available";
                    $statusSym = "&#x2713;";
                    $statusClass = "green";
                    break;
                case 2:
                    $statusText = "Away From Desk<br />Return: ".$row['return'];
                    $statusSym = "&#8709;";
                    $statusClass = "blue";
                    break;
                case 3:
                    $statusText = "At Lunch<br />Return: ".$row['return'];
                    $statusSym = "&#8709;";
                    $statusClass = "blue";
                    break;
                case 4:
                    $statusText = "Out For Day<br />Return: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                case 5:
                    $statusText = "Out<br />Return: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                case 6:
                    $statusText = "Appointment<br />Return: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                case 7:
                    $statusText = "Do Not Disturb<br />Return: ".$row['return'];
                    $statusSym = "&#x2717;&#x2717;";
                    $statusClass = "red";
                    break;
                case 8:
                    $statusText = "Meeting<br />Return: ".$row['return'];
                    $statusSym = "&#8709;";
                    $statusClass = "blue";
                    break;
                case 9:
                    $statusText = "Out Sick<br />Return: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                case 10:
                    $statusText = "Vacation<br />Return: ".$row['return'];
                    $statusSym = "&#x2717;";
                    $statusClass = "red";
                    break;
                default:
                    $statusText = "Unknown Status<br />Notify Dandelion Admin";
                    $statusSym = "?";
                    $statusClass = "red";
                    break;
            }
            
            echo '<td class="statusi"><span class="' . $statusClass . '">' . $statusSym . '</span></td><td>' . $statusText . '</td><td>' . $row['dmodified'] . '</td></tr>';
        }
        echo '</tbody></table>';
    }
}