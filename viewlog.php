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
		$add_link = '<input type="button" onClick="addFun.showaddinputs();" value="Add New Log Entry" />';
	}
	else {
		$add_link = '';
		$settings_link = '';
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
		<link rel="stylesheet" type="text/css" href="styles/presence.css" />
		<link rel="stylesheet" type="text/css" href="jquery/css/smoothness/jquery-ui.min.css" />
		<title>Dandelion Web Log</title>
	</head>
	<body onLoad="refreshFun.startrefresh();">
    
        <div id="presence">
            <h3>&#264;eesto:</h3>
            <form method="post">
                <select id="cstatus">
                    <option>Set Status:</option>
                    <option>Available</option>
                    <option>Away From Desk</option>
                    <option>At Lunch</option>
                    <option>Out for Day</option>
                    <option>Out</option>
                    <option>Appointment</option>
                    <option>Do Not Disturb</option>
                    <option>Meeting</option>
                    <option>Out Sick</option>
                    <option>Vacation</option>
                </select>
                <input type="button" value="Set" onClick="presence.setStatus(0);" />
            </form>
            <div id="pt"></div>
        </div>
    
        <div id="divMain">
            <header>
                <?php include 'scripts/header.php'; ?>
            </header>
            
            <form id="category" method="post">
                Filter: 
                <select name="f_cat_1" id="f_cat_1" onchange="f_pop_cat_2(this)">
                    <option value="select">Select:</option>
                    <option value="Desktop">Desktop</option>
                    <option value="Appliances">Appliances</option>
                    <option value="Network">Network</option>
                    <option value="Servers">Servers</option>
                    <option value="UPS">UPS</option>
                </select>
                <select name="f_cat_2" id="f_cat_2" onchange="f_pop_cat_3(this)" value="select">
                    
                </select>
                <select name="f_cat_3" id="f_cat_3" onchange="f_pop_cat_4(this)">
                    
                </select>
                <select name="f_cat_4" id="f_cat_4" onchange="f_pop_cat_5(this)">
                    
                </select>
                <select name="f_cat_5" id="f_cat_5">
                    
                </select>
                <input type="button" value="Filter" onClick="refreshLog('filter');" /><br />
                <input type="text" id="searchterm" size="40" value="Keyword" onClick="miscFun.clearval(this);" onKeyPress="return searchFun.check(event);" /><input type="text" id="datesearch" size="10" value="Date" onClick="miscFun.clearval(this);" />
                <input type="button" value="Search Log" onClick="searchFun.searchlog();" />
            </form>
            
            <form>		
                <div id="refreshbutton" style="display:inline;"></div>
                <div id="rcounter" class="good" style="display:inline;">120 sec</div>
                
                <?php echo $add_link; ?>
            </form>
            
            <p id="gentime"></p>
            
            <div id="add_edit"></div>
            
            <div id="refreshed"></div>
            
            <footer>
                <p id="credits">&copy; 2013 Daedalus Computer Services</p>
            </footer>
            <script src="jquery/js/jquery-1.9.1.js"></script>
            <script src="jquery/js/jquery-ui-1.10.3.custom.min.js"></script>
            <script src="scripts/categories.js"></script>
            <script src="scripts/mainScripts.js"></script>
            <script src="scripts/presence.js"></script>
        </div>
	</body>
</html>
