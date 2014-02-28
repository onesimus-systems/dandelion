<?php

/** Global function to check if a user is logged in to Dandelion
 */
function checkLogIn() {
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

function getTheme() {
	$theme = !empty($_SESSION['userInfo']['theme']) ? $_SESSION['userInfo']['theme'] : 'default';
	$theme = is_dir(THEME_DIR.$theme) ? $theme : 'default';
	return $theme;
}