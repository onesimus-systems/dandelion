<!DOCTYPE html>

<?php
include 'scripts/dbconnect.php';
error_reporting(E_ALL);

//Check for auth cookie, if set check against sessiontoken table to see it session is still valid
$cookie = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '';
if ($cookie) {
    list ($user, $token, $mac) = explode(':', $cookie);
	
	$query = mysqli_query($con, 'SELECT * FROM session_token WHERE userid = "' . $user . '"');
	
	//The token from the cookie had an extra character at the end, this trims it off
	//This is because the token is 256 chars long, the DB only holds 255
	$trimtoken = substr($token, 0, -1);
	$auth_user = mysqli_fetch_array($query);
	$hasexpired = $auth_user['expire'];
	
    if ($mac === hash_hmac('sha256', $user . ':' . $token, "usi.edu") AND $auth_user['token'] === $trimtoken AND $hasexpired >= time()) {
		mysqli_close($con);
        header( 'Location: viewlog.php' );
    }
	elseif ($hasexpired > time()) {
		echo $status .= "<br />Your session has expired.";
	}
}
?>

<html>
	<head>
		<meta charset="utf-8" />
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
		<title>Dandelion Web Log</title>
	</head>
	<body>
		<div id="login">
			<h1>Dandelion Web Log</h1>
            <?php echo $status ?>
			<form name="login_form" action="scripts/login.php" method="post">
			Username:<br /><input type="text" value="" name="in_name" autocomplete="off" autofocus /><br />
			Password:<br /><input type="password" value="" name="in_pass" /><br />
			<input type="submit" value="Login" />
			</form>		
			</div>
	</body>
</html>
