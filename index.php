<!DOCTYPE html>

<?php
/*
 * Lee Keitel
 * January 27, 2014
 *
 * This page is the home page, hence the index filename.
 * When loading it checks to see if there is already a
 * running session for the user and if so, direct them
 * to the log.
*/

/*! \mainpage Dandelion Weblog
 *
 *  \section intro_sec Introduction
 *
 * Dandelion was conceived after thinking of a way to replace the aging and slow Bloxom-based
 * we were currently using to document change logs. I wanted to keep the web-based system,
 * use an SQL database instead of text files, and make every action possible via the browser
 * instead of having to SSH into the Blosxom server.
 *
 * And that is how Dandelion was born.
 */

include_once 'scripts/dbconnect.php'; //Required for DB class and DB connection

if (checkLogIn()) {
    header( 'Location: viewlog.phtml' );
}

else {
    $status = '';
    $showlogin = true;
    $badlogin = isset($_SESSION['badlogin']) ? $_SESSION['badlogin'] : false;
    
    if ($badlogin) {
        $status = '<span class="bad">Incorrect username or password</span><br />';
    }
	
	$theme = getTheme();
}
?>

<html>
	<head>
		<meta charset="utf-8" />
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
		<link rel="stylesheet" type="text/css" href="themes/<?php echo $theme;?>/main.css" />
		<title>Dandelion Web Log</title>
	</head>
	<body>
		<div id="login">
			<h1>Dandelion Web Log</h1>
            <?php echo $status;
            
            if ($showlogin) {?>
                <form name="login_form" action="scripts/login.php" method="post">
                Username:<br /><input type="text" value="" name="in_name" autocomplete="off" autofocus /><br />
                Password:<br /><input type="password" value="" name="in_pass" /><br />
                <input type="submit" value="Login" id="login_button" />
                </form>
            <?php }
            ?>
			</div>
	</body>
</html>
