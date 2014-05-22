<?php
/**
  * This file handles login functions and
  * checks authentication of logged in user.
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion\Gatekeeper;

use Dandelion\Database\dbManage;
use Dandelion\Permissions;

require_once (is_file('bootstrap.php')) ? 'bootstrap.php' : 'scripts/bootstrap.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["in_name"])) {
    login();
}

function authenticated()
{
    $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

    if ($loggedin) { // If a current PHP session is running, log in
        return true;
    }

    // No session and no session token, need to log in
    return false;
}

function login()
{
    $username = $_POST["in_name"];
    $plain_word = $_POST["in_pass"];
    $userInfo = isUser($username, $plain_word);

    if ($userInfo) {
        if (ini_get("session.use_cookies")) {
            setcookie(session_name(), $_COOKIE[session_name()], time()+60*60*22, '/');
        }
        $_SESSION['loggedin'] = true;

        $_SESSION['userInfo'] = $userInfo;

        $myPermissions = new Permissions();
        $_SESSION['rights'] = (array) $myPermissions->loadRights($_SESSION['userInfo']['role']);

        if (isset($_POST['rememberMe']) && $_POST['rememberMe'] == 'remember') {
            setcookie('dan_username', $_SESSION['userInfo']['username'], time()+60*60*24*30, '/');
        }

        trigger_error($username.' logged in at ' . date("Y-m-d H:i:s"), E_USER_NOTICE);

        switch($userInfo['firsttime']) {
            case 2:
                header ( 'Location: ../reset.phtml' );
                break;
            default:
                header( 'Location: ../' );
                break;
        }
    } else {
        trigger_error('Failed login attempt for '.$username.' at ' . date("Y-m-d H:i:s"), E_USER_WARNING);
        $_SESSION['badlogin'] = '<span class="bad">Incorrect username or password</span><br>'; // Used to display a message to the user
        header( 'Location: ../' );
    }
}

// Determines if the person is a user or not
// If yes, validates and redirects to viewlog.phtml
// If no, yells at user, loudly
function isUser($uname, $pword)
{
    $conn = new dbManage();

    $stmt = 'SELECT * FROM `'.DB_PREFIX.'users` WHERE `username` = :user';
    $param = array('user' => $uname);
    /** @noinspection PhpUndefinedMethodInspection */
    $user = $conn->queryDB($stmt, $param);

    if ($user[0]['password'] && password_verify($pword, $user[0]['password'])) { // Check if password is correct
        return $user[0];
    }

    return false;
}

function logout()
{
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    header( 'Location: ../' );
}
