<?php
include 'dbconnect.php';

$cookie = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '';
if ($cookie) {
    list ($user, $token, $mac) = explode(':', $cookie);
	mysqli_query($con, 'DELETE FROM session_token WHERE userid = "' . $user . '"');
}

setcookie($cookie_name, "", time() - 3600);

echo 'You are now logged out!';
header( 'Location: ../index.php' );