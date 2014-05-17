<?php
/**
  * Handle requests to save user's settings
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

$limit = $_SESSION['userInfo']['showlimit'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u_action = isset($_POST['set_action']) ? $_POST['set_action'] : '';
    $sub_action = isset($_POST['sub_act']) ? $_POST['sub_act'] : '';

    if ($u_action == "Reset Password") {

        //Form to reset user's password
        ?>
        <div id="editform">
        <h2>Reset Password for <?php echo $_SESSION['userInfo']['realname']; ?>:</h2>
        <form name="edit_form" method="post">
            <table>
                <tr><td>New Password:</td><td><input type="password" name="reset_1"></td></tr>
                <tr><td>Repeat Password:</td><td><input type="password" name="reset_2"></td></tr>
            </table>
            <input type="submit" name="sub_act" value="Reset">
        </form></div><br>
        <?php
    } elseif ($u_action == "Save Limit") {
        $showlimit = $_POST['show_limit'];

        if ($showlimit >= 5 AND $showlimit <= 500) {
            $conn = new dbManage();

            $stmt = 'UPDATE `'.DB_PREFIX.'users` SET `showlimit` = :newlimit WHERE `userid` = :myID';
            $params = array(
                'newlimit' => $showlimit,
                'myID' => $_SESSION['userInfo']['userid']
            );
            $conn->queryDB($stmt, $params);

            echo 'Show limit change successful.<br><br>';

            $limit = $_SESSION['userInfo']['showlimit'] = $showlimit;
        } else {
            echo "Please choose a number between 5-500 for log view limit.<br><br>";
        }
    } elseif ($u_action == 'Save Theme') {
        $newTheme = isset($_POST['userTheme']) ? $_POST['userTheme'] : 'default';

        $conn = new dbManage();

        $stmt = 'UPDATE `'.DB_PREFIX.'users` SET `theme` = :theme WHERE `userid` = :myID';
        $params = array(
            'theme' => $newTheme,
            'myID' => $_SESSION['userInfo']['userid']
        );
        $conn->queryDB($stmt, $params);

        $_SESSION['userInfo']['theme'] = $newTheme;

        // Reload page to show new theme
        header('Location: settings.phtml');
    }

    if ($sub_action == "Reset") {
        $reset_3 = $_POST['reset_1'];
        $reset_4 = $_POST['reset_2'];

        if ($reset_3 == $reset_4) {
            require_once ROOT.'/classes/users.php';
            $useractions = new User(new dbManage());
            $useractions->resetUserPw($_SESSION['userInfo']['userid'], $reset_3);

            echo 'Password change successful.<br><br>';
        } else {
            echo 'New passwords do not match<br><br>';
        }
    }
}
