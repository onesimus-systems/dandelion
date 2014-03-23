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

require_once (is_file('grabber.php')) ? 'grabber.php' : 'scripts/grabber.php';

$cookie_name = 'dandelionrememt'; 	// Used for login remembering (soon to go away)

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["in_name"])) {
	login();
}

function authenticated() {
	//Check for auth cookie, if set check against session_token table to see it session is still valid
	global $cookie_name;
	$loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;
	$cookie = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : false;

	if ($loggedin) { // If a current PHP session is running, log in
		return true;
	}

	/* If a PHP session has expired, but a person is still logged in,
	 * Replace the loggedin and realName session variables, and go in
	*
	* This function will soon go away. I have since learned more about
	* PHP sessions and will able to remove this function.
	*/
	if ($cookie) {
		// Connect to DB
		$conn = new dbManage;

		list ($user, $token, $mac) = explode(':', $cookie);

		// Grab information from session_token
		$stmt = 'SELECT * FROM session_token WHERE userid = :id';
		$params = array('id' => $user);
		$auth_user = $conn->queryDB($stmt, $params);

		// If a result was returned, check if it has expired
		if (isset($auth_user['expire'])) {
			if ($mac === hash_hmac('sha256', $user . ':' . $token, "usi.edu")
			AND $auth_user[0]['token'] === $token
			AND $auth_user[0]['expire'] >= time()) {

				$stmt = 'SELECT * FROM users WHERE userid = :user';
				$param = array('user' => $user);

				$sel_user = $conn->queryDB($stmt, $param);

				$_SESSION['userInfo'] = $sel_user[0];
				$_SESSION['loggedin'] = true;
				return true;
			}
		}
	}

	// No session and no session token, need to log in
	else {
		return false;
	}
}

function login() {
	echo "<p>Logging in...</p>";
	
	// Declare and clear variables for login info
	$username = $plain_word = "";

	// Connect to DB
	$conn = new dbManage();

	$username = $_POST["in_name"];
	$plain_word = $_POST["in_pass"];
	
	// Begin login procedure
	isuser($username, $plain_word, $conn, $cookie_name);
}
	
// Determines if the person is a user or not
// If yes, validates and redirects to viewlog.phtml
// If no, yells at user, loudly
function isuser($uname, $pword, $conn, $cookie_name) {

	// First, is this person even a user?

	$stmt = 'SELECT * FROM users WHERE username = :user';
	$param = array('user' => $uname);

	$sel_user = $conn->queryDB($stmt, $param);

	if ($sel_user[0]['password']) { // Check if password is correct
		$goodToGo = password_verify($pword, $sel_user[0]['password']);
	}
	else {
		$goodToGo = false;
	}

	// So they are!! Well, make a token for them!
	if ($goodToGo) {
		beginSess($sel_user[0]['userid'], $conn, $cookie_name);

		$_SESSION['userInfo'] = $sel_user[0];

		$stmt = 'SELECT `value` FROM `settings` WHERE `name` = "slogan"';

		$_SESSION['settings']['slogan'] = $conn->queryDB($stmt, NULL)[0]['value'];

		echo 'Logged in. Please wait as I redirect you...';

		// Make this into switch statement
		if ($sel_user[0]['firsttime'] == 1) {
			header ( 'Location: ../tutorial.phtml' );
		}
		elseif ($sel_user[0]['firsttime'] == 2) {
			header ( 'Location: ../reset.phtml' );
		}
		elseif ($sel_user[0]['firsttime'] == 3) {
			header ( 'Location: ../whatsnew.phtml' );
		}
		else {
			//header( 'Location: ../viewlog.phtml' );
			header( 'Location: ../' );
		}
	}
	else { // Sadly they have failed. Walk the plank!
		$_SESSION['badlogin'] = true; // Used to display a message to the user
		header( 'Location: ../index.php' );
	}
}

// Generate a token, put it in the DB and as a cookie for auth
function beginSess($user, $conn, $cookie_name) {
	$token = openssl_random_pseudo_bytes(128);
	$token = bin2hex($token);
	$extime = time()+60*60*20;

	$stmt = 'SELECT * FROM session_token WHERE userid = :id';
	$param = array('id' => $user);

	$auth_user = $conn->queryDB($stmt, $param);

	// If there is a token, delete it before setting new one
	if ($auth_user[0]['session_id']) {

		$stmt = 'DELETE FROM session_token WHERE userid = :id';
		$param = array('id' => $user);

		$conn->queryDB($stmt, $param);
	}

	// Set session token

	$stmt = 'INSERT INTO session_token (token, userid, expire) VALUES(:sshtoken,:uid,:exp)';
	$param = array(
			'sshtoken' => $token,
			'uid' => $user,
			'exp' => $extime
	);

	$conn->queryDB($stmt, $param);

	// Set cookie for token
	$cookie = $user . ':' . $token;
	$mac = hash_hmac('sha256', $cookie, "usi.edu");
	$cookie .= ':' . $mac;
	setcookie($cookie_name, $cookie, 0, "/");
	$_SESSION['loggedin'] = true;
	echo "Cookie Set";
}

function logout() {
	setcookie($cookie_name, "", time()-3600, "/");   // Delete token cookie
	setcookie(session_name(), "", time()-3600, "/"); // Delete Session cookie
	
	echo 'You are now logged out!';     // Little message in case it takes a moment
	header( 'Location: ../index.php' ); // To the login page with you!
}