<?php
/**
  * Handle requests to reset user passwords
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;
use Dandelion\Database\dbManage;

require_once 'bootstrap.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reset_3 = isset($_POST['reset_1']) ? $_POST['reset_1'] : '';
    $reset_4 = isset($_POST['reset_2']) ? $_POST['reset_2'] : '';

    if ($reset_3 == $reset_4) {
        $conn = new dbManage();
        $useractions = new Users\User();
        $useractions->resetUserPw($_SESSION['userInfo']['userid'], $reset_3);

        $stmt = 'UPDATE `'.DB_PREFIX.'users` SET `firsttime` = 0 WHERE `userid` = :id';
        $params = array('id'=>$_SESSION['userInfo']['userid']);
        $conn->queryDB($stmt, $params);

        header( 'Location: ../lib/logout.php' );
    } else {
        $_SESSION['errors'] = '<br><span class="bad">Entered passwords do not match. Please try again.</span><br><br>';
        header( 'Location: ../reset.php' );
    }
} else {
    header( 'Location: ../viewlog.php' );
}
