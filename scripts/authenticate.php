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

/**
 * If this file is called from the login page, it will contain POST data
 * with a field called in_name.
 *
 * If this happens a user is attempting to login.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["in_name"])) {
    login();
}

/**
 * Simple function to determine if a user is logged in or not.
 *
 * @return bool
 */
function authenticated()
{
    $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

    return $loggedin;
}

/**
 * Perform a user logon.
 *
 * Get the username and password provided.
 * Check to see if the user exists and if the password is correct.
 * If true, set session variables and route to pages as needed.
 * If false, display error message.
 */
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
                header ( 'Location: ../reset.php' );
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

/**
 * Checks if a provided username is an actual user
 * and if the provided password is correct.
 *
 * @param string $uname - Username
 * @param string $pword - Password
 *
 * @return bool or array - Array containing row of user data from database, false on error
 */
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

/**
 * Perform a logout by destroying the session.
 */
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
