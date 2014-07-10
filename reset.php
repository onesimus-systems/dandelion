<?php
/**
 * This page is seen by a person once after their account
 * is initially created. It updates their record with an Bcrypt
 * encrypted password and changes firsttime to 1 so they
 * are redirected to the tutorial on next login.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 27, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

require_once 'lib/bootstrap.php';

// Check whether the user is logged in
if (!Gatekeeper\authenticated()) {
	header( 'Location: index.php' );
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<?php echo loadCssSheets("tutorial"); ?>
		<title>Dandelion Web Log - Tutorial</title>
	</head>
	<body>
        <header>
            <h1 class="t_cen">Dandelion Web Log - v <?php echo D_VERSION ?></h1>
            <p class="t_cen">Welcome, <?php echo $_SESSION['userInfo']['realname']; ?> <a href="lib/logout.php">Logout</a></p>
        </header>
        
        <p>This is your first time logging into Dandelion. Please reset your password:</p>
		
        <br />
        <?php echo (isset($_SESSION['errors']) ? $_SESSION['errors'] : ''); $_SESSION['errors']=''; ?>
        <br />
        
		<div id="editform">
			<form name="edit_form" method="post" action="scripts/resetpw.php">
				<table>
					<tr><td>New Password:</td><td><input type="password" name="reset_1" /></td></tr>
					<tr><td>Repeat Password:</td><td><input type="password" name="reset_2" /></td></tr>
				</table>
				<input type="submit" name="sub_act" value="Reset" />
			</form>
		</div>
		
		<br />
        
        <footer>
            <?php include_once 'scripts/footer.php'; ?>
        </footer>
	</body>
</html>
