<?php
/*
 * @author Lee Keitel
 * @date February 3, 2014
 *
 * This script deletes the cookies for authentication
 * and the PHP session effectivly logging out the user.
*/

include_once 'dbconnect.php';

setcookie($cookie_name, "", time()-3600, "/");   // Delete token cookie
setcookie(session_name(), "", time()-3600, "/"); // Delete Session cookie

echo 'You are now logged out!';     // Little message in case it takes a moment
header( 'Location: ../index.php' ); // To the login page with you!