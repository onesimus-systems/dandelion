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
    
    if ($user_info['firsttime'] == 1) {
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
		<title>Dandelion Web Log - Tutorial</title>
	</head>
	<body>
        <header>
            <?php include 'scripts/header.php'; ?>
        </header>
		
		<h2 class="t_cen">Tutorial</h2>
        
        <p class="le">Welcome to the Dandelion Web Log. This brief tutorial will walk you through how to do all the basic tasks in Dandelion. <?php if ($first == true) { echo "When you're ready, click the Homepage link at the top to begin using Dandelion."; } ?></p>
        
        <h3>Reset Password</h3>
        <p class="le">To reset your password:
            <ol>
                <li>Click "Settings"</li>
                <li>Click "Reset Password"</li>
                <li>Enter new password twice</li>
                <li>Click Reset</li>
                <li>Dance</li>
            </ol></p>
            
        <h3>Add a log entry</h3>
        <p class="le">To add a log entry:
            <ol>
                <li>On the homepage, click "Add New Log entry" at the top of the page</li>
                <li>Fill in the title, entry, and category</li>
                <li>Click "Add Log"</li>
            </ol></p>
        
        <h3>Edit a log entry</h3>
        <p class="le">To edit a log entry:
            <ol>
                <li>Click "Edit" next to the entry you want to edit (you can only edit entries you entered)</li>
                <li>Edit the text as needed</li>
                <li>Click "Save Edit"</li>
            </ol></p>
            
        <h3>Search Logs</h3>
        <p class="le">To search for a log entry:
            <ol>
                <li>Type in keywords in the Keyword box and/or click the Date field to select a date</li>
                <li>Click "Search Log"</li>
                <li>Click "Clear Search" to return to the live log</li>
            </ol></p>
            
        <h3>Filter Logs by Category</h3>
        <p class="le">To edit a log entry:
            <ol>
                <li>Select the desired category by choosing from the drop down boxes next to Filter</li>
                <li>Click "Filter"</li>
                <li>Click "Clear Filter" to return to the live log</li>
            </ol></p>
            
        <h3>Using &#264;eesto</h3>
        <p class="le">To set your current status:
            <ol>
                <li>Select your status from the dropdown under Presence</li>
                <li>Click "Set"</li>
                <li>Enter any extra data as needed</li>
            </ol></p>
            
        <p class="le">To see someone's status:
            <ol>
                <li>Hover your mouse over the status icon to view its meaning</li>
                <li>If the person is set as Away or Out For the Day, their return time will also be shown</li>
            </ol></p>
            
        <p class="le">To see someone's message:
            <ol>
                <li>Hover your mouse over the person's name to view any message they left</li>
            </ol></p>
        
        <footer>
            <p id="credits" class="t_cen">&copy; 2013 Daedalus Computer Services</p>
        </footer>
	</body>
</html>
