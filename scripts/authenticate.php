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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["in_name"])) {
	login();
}

function authenticated() {
	$loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

	if ($loggedin) { // If a current PHP session is running, log in
		return true;
	}
	
	// No session and no session token, need to log in
	return false;
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
	isuser($username, $plain_word, $conn);
}
	
// Determines if the person is a user or not
// If yes, validates and redirects to viewlog.phtml
// If no, yells at user, loudly
function isuser($uname, $pword, $conn) {

	// First, is this person even a user?
	$stmt = 'SELECT * FROM `'.DB_PREFIX.'users` WHERE `username` = :user';
	$param = array('user' => $uname);
	
	$sel_user = $conn->queryDB($stmt, $param);
	
	if ($sel_user[0]['password']) { // Check if password is correct
		$goodToGo = password_verify($pword, $sel_user[0]['password']);
	}
	else {
		$goodToGo = false;
	}

	// So they are!!
	if ($goodToGo) {
		if (ini_get("session.use_cookies")) {
			setcookie(session_name(), $_COOKIE[session_name()], time()+60*60*22, '/');
		}
		$_SESSION['loggedin'] = true;

		$_SESSION['userInfo'] = $sel_user[0];
		
		if ($_POST['rememberMe'] == 'remember') {
			setcookie('dan_username', $_SESSION['userInfo']['username'], time()+60*60*24*30, '/');
		}

		echo 'Logged in. Please wait as I redirect you...';
		
		switch($sel_user[0]['firsttime']) {
			case 1:
				header ( 'Location: ../tutorial.phtml' );
				break;
			case 2:
				header ( 'Location: ../reset.phtml' );
				break;
			default:
				header( 'Location: ../' );
				break;
		}
	}
	else { // Sadly they have failed. Walk the plank!
		$_SESSION['badlogin'] = '<span class="bad">Incorrect username or password</span><br>'; // Used to display a message to the user
		header( 'Location: ../' );
	}
}

function logout() {
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
		);
	}
	session_destroy();
	
	echo 'You are now logged out!';     // Little message in case it takes a moment
	header( 'Location: ../' ); // To the login page with you!
}