<!DOCTYPE html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', True);

include 'dbconnect.php';

echo "<p>Logging in...</p>";

//Declare and clear variables for login info
$username = $plain_word = "";

//Was there a login attempt? If so validate data
if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $username = vali($_POST["in_name"]);
        $plain_word = vali($_POST["in_pass"]);
        $plain_word = sha1($plain_word);
        isuser($username, $plain_word, $con, $cookie_name);
    }

//Determines if the person is a user or not
//If yes, validates and redirects to viewlog.php
//If no, yells at user
function isuser($uname, $pword, $con, $cookie_name) {
	$query = mysqli_query($con, "SELECT * FROM users WHERE username = '" . $uname . "' AND password = '" . $pword . "'");

	$sel_user = mysqli_fetch_array($query);
	
	if ($sel_user['userid'] != null) {
		beginSess($sel_user['userid'], $con, $cookie_name);
		setcookie("RealName", $sel_user['realname'], 0, "/");
        
        if ($sel_user['firsttime'] == 1) {
            header ( 'Location: ../tutorial.php' );
        }
		elseif ($sel_user['firsttime'] == 2) {
            header ( 'Location: ../reset.php' );
        }
		elseif ($sel_user['firsttime'] == 3) {
            header ( 'Location: ../whatsnew.php' );
        }
        else {
            header( 'Location: ../viewlog.php' );
        }
	}
	else {
		header( 'Location: ../index.php' );
	}
	mysqli_close($con);
}

//Generate a token, put it in the DB and as a cookie for auth
function beginSess($user, $con, $cookie_name) {
    $token = openssl_random_pseudo_bytes(128);
    $token = bin2hex($token);
	//echo $token;
	$extime = time()+60*60*20;

	//Check to see if this user already has a session token in the DB
	$query = mysqli_query($con, 'SELECT * FROM session_token WHERE userid = "' . $user . '"');
	$auth_user = mysqli_fetch_array($query);
	
	//If there is a token, delete it before setting new one
	if ($auth_user['session_id'] != null) {
		mysqli_query($con, 'DELETE FROM session_token WHERE userid = "' . $user . '"');
	}
	
	//Set new token
	$qu = 'INSERT INTO session_token (session_id, token, userid, expire) VALUES ("", "' . $token . '", "' . $user . '", "' . $extime . '")';
	
	if (!mysqli_query($con, $qu)) {
		die('<br /><br />Error creating session token: ' . mysqli_error($con));
	}
	else {
		//Set cookie for token
		$cookie = $user . ':' . $token;
		$mac = hash_hmac('sha256', $cookie, "usi.edu");
		$cookie .= ':' . $mac;
		setcookie($cookie_name, $cookie, 0, "/");
		echo "Cookie Set";
	}
}