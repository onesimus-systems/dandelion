<!DOCTYPE html>

<?php
/**
  * This is the homepage of Dandelion.
  * If a user is already logged in it will
  * redirect them to the viewlog page.
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

/*! \mainpage Dandelion Weblog
 *
 *  \section intro_sec Introduction
 *
 * Dandelion was conceived after thinking of a way to replace the aging and slow Bloxom-based
 * log we were currently using to document change logs. I wanted to keep the web-based system,
 * use an SQL database instead of text files, and make every action possible via the browser
 * instead of having to SSH into the Blosxom server.
 *
 * And that is how Dandelion was born.
 */

include_once 'scripts/grabber.php'; //Required for DB class and DB connection

if (!$_SESSION['config']['installed']) {
	header( 'Location: install.php' );
}

if (authenticated()) {
    header( 'Location: viewlog.phtml' );
}

else {
    $status = '';
    $showlogin = true;
    $badlogin = isset($_SESSION['badlogin']) ? $_SESSION['badlogin'] : false;
    
    if ($badlogin) {
        $status = '<span class="bad">Incorrect username or password</span><br>';
    }
	
	$theme = getTheme();
}
?>

<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
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
                <form name="login_form" action="scripts/authenticate.php" method="post">
                Username:<br /><input type="text" value="" name="in_name" autocomplete="off" autofocus /><br />
                Password:<br /><input type="password" value="" name="in_pass" /><br />
                <input type="submit" value="Login" id="login_button" />
                </form>
            <?php }
            ?>
			</div>
	</body>
</html>
