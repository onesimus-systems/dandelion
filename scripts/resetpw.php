<?php
include_once 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $reset_3 = isset($_POST['reset_1']) ? $_POST['reset_1'] : ''; // Password 1
    $reset_4 = isset($_POST['reset_2']) ? $_POST['reset_2'] : ''; // Password 2
    
    if ($reset_3 == $reset_4) { // Do they match?
        $reset_3 = password_hash($reset_3, PASSWORD_BCRYPT); // Hash the password if they match
        
        // Connect to DB
        $conn = new dbManage();
        
        // Update record with new password and change firsttime
        $stmt = 'UPDATE `users` SET `password` = :newpass, `firsttime` = 1 WHERE `userid` = :myID';
        $params = array(
            'newpass' => $reset_3,
            'myID' => $_SESSION['userInfo']['userid']
        );
        $conn->queryDB($stmt, $params);
        
        echo 'Password Reset<br /><br />';
        header( 'Location: logout.php' );
    }
    else {
        $_SESSION['errors'] = '<br /><span class="bad">Entered passwords do not match. Please try again.</span><br /><br />';
        header( 'Location: ../reset.phtml' );
    }
}
else {
    header( 'Location: ../viewlog.phtml' );
}