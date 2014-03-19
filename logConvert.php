<?php
/*
 * Lee Keitel
 * January 28, 2014
 *
 * This script converts the `log` table from a user's
 * real name to their user id #. This conversion is
 * necessary for upgrading from Dandelion 3.1 to 4.0.
 * This script is only needed once during upgrade.
*/

include_once 'scripts/grabber.php';

// Connect to DB
$conn = new dbManage();

// Grab a list of all current users and put them in an array
$userArrayData = 'SELECT `userid`,`realname` FROM `users`';
$userArray = $conn->queryDB($userArrayData, NULL);

// Grab a list of all log entries and put them in an array
$logArrayData = 'SELECT `logid`,`usercreated` FROM `log`';
$logArray = $conn->queryDB($logArrayData, NULL);

$i = 1;

$start = microtime(true); // Start time for process

// For every log entry, look up its corresponding userID
foreach ($logArray as $value) {
    $creatorId = 0; // Place holder for entry's userID
    
    // Cycle through all users to find which one the entry belongs to
    foreach ($userArray as $user) {
        if ($value['usercreated'] == $user['realname']) {
            $creatorId = $user['userid'];
            break;
        }
    }
    
    // Feedback of logid, the real name of user, and userid
    echo $i . ' | ' . $value['logid'] . ' | ' . $value['usercreated'] . ' | ' . $creatorId . '<br />';
    
    // Write back the userID in place of the realname
    if ($creatorId > 0)
    {
        $logConvert = 'UPDATE `log` SET `usercreated` = :userid WHERE `logid` = :logid';
        $params = array(
            'userid' => $creatorId,
            'logid' => $value['logid']
        );
        $conn->queryDB($logConvert, $params);
        
        echo 'Converted entry number ' . $i . ' successfully.<br /><br />';
    }

    $i++; // Increment counter for sequential log numbers
}
$end = microtime(true); // End timer for process

echo '<br />This process completed in ' . ($end - $start) . ' seconds.';
?>