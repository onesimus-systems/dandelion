<?php
/**
  * Handle requests to save edits to log entries
  *
  * This file is a part of Dandelion
  * 
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

require_once 'grabber.php';
require_once ROOT.'/classes/users.php';


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $reset_3 = isset($_POST['reset_1']) ? $_POST['reset_1'] : '';
    $reset_4 = isset($_POST['reset_2']) ? $_POST['reset_2'] : '';
    
    if ($reset_3 == $reset_4) {
        $conn = new dbManage();
        $useractions = new User($conn);
        $useractions->resetUserPw($_SESSION['userInfo']['userid'], $reset_3);
        
        $stmt = 'UPDATE `'.DB_PREFIX.'users` SET `firsttime` = 1 WHERE `userid` = :id';
        $params = array('id'=>$_SESSION['userInfo']['userid']);
        $conn->queryDB($stmt, $params);
        
        header( 'Location: logout.php' );
    }
    else {
        $_SESSION['errors'] = '<br><span class="bad">Entered passwords do not match. Please try again.</span><br><br>';
        header( 'Location: ../reset.phtml' );
    }
}
else {
    header( 'Location: ../viewlog.phtml' );
}