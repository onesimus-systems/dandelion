<?php
include 'dbconnect.php';
error_reporting(E_ALL);
ini_set('display_errors', True);

//Grab the name of the user
$cookie = isset($_COOKIE['RealName']) ? $_COOKIE['RealName'] : '';
if ($cookie) {
    $realname = $cookie;
}

//Authenticate user, if fail go to login page
$cookie = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '';
if ($cookie) {
    list ($user, $token, $mac) = explode(':', $cookie);
	
	$query = mysqli_query($con, 'SELECT * FROM session_token WHERE userid = "' . $user . '"');
	$userinfo = mysqli_query($con, 'SELECT * FROM users WHERE userid = "' . $user . '"');
	
	//The token from the cookie had an extra character at the end, this trims it off
	$trimtoken = substr($token, 0, -1);
	$auth_user = mysqli_fetch_array($query);
	$user_info = mysqli_fetch_array($userinfo);
	$hasexpired = $auth_user['expire'];
	
    if ($mac !== hash_hmac('sha256', $user . ':' . $token, "usi.edu") OR $auth_user['token'] !== $trimtoken OR $hasexpired < time()) {
		mysqli_close($con);
        header( 'Location: index.php' );
    }
}
else {
	header( 'Location: index.php' );
}