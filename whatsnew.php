<!DOCTYPE html>

<?php
include 'scripts/dbconnect.php';
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
    $first = false;
	
    if ($mac !== hash_hmac('sha256', $user . ':' . $token, "usi.edu") OR $auth_user['token'] !== $trimtoken OR $hasexpired < time()) {
		mysqli_close($con);
        header( 'Location: index.php' );
    }
	elseif ($user_info['role'] == "guest") {
		header( 'Location: viewlog.php' );
	}
	
	if ($user_info['role'] === "admin") {
		$admin_link = '| <a href="admin.php">Administration</a>';
	}
	else {
		$admin_link = '';
	}
	
	if ($user_info['role'] !== "guest") {
		$settings_link = '| <a href="settings.php">Settings</a>';
	}
	else {
		$settings_link = '';
	}
    
    if ($user_info['firsttime'] == 3) {
        $first = true;
        mysqli_query($con, 'UPDATE users SET firsttime = 0 WHERE userid = "'.$user_info['userid'].'"');
    }
}
else {
	header( 'Location: index.php' );
}
?>
<html>
	<head>
		<meta charset="utf-8" />
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
        <link rel="stylesheet" type="text/css" href="styles/tutorial.css" />
		<title>Dandelion Web Log - What's New</title>
	</head>
	<body>
        <header>
            <?php include 'scripts/header.php'; ?>
        </header>
		
		<h2 class="t_cen">New Features</h2>
        
        <p class="le">New features have been added to Dandelion! Check them out below:</p>
		
		<p class="le">
			<ul>
				<li>Form Validation has been lightened to allow for more characters to be used</li>
                <li><b>Presence has been added to Dandelion! Now you can tell everyone where you are right from Dandelion. Set your status by using the Presence box in the upper left corner. This box is only visible on the <u>main View Log page</u>.</b></li>
                
			</ul>
		</p>
        
        <p class="le">In the works:</p>
		
		<p class="le">
			<ul>
				<li>Better visuals.</li>
                <li>More/faster functionality through jQuery</li>
                <li>Ease of use.</li>
			</ul>
		</p>
        
        <p>If you have any feature suggestions or need to report a bug, email Lee Keitel at <a href="mailto:lfkeitel@usi.edu">lfkeitel@usi.edu</a></p>
        
        <p><a href="viewlog.php">Continue to Dandelion --></a></p>
        
        <footer>
            <p id="credits" class="t_cen">&copy; 2014 Daedalus Computer Services</p>
        </footer>
	</body>
</html>
