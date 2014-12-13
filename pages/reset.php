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

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

include 'static/includes/head.php';
?>
<!-- Begin Page Body -->
<p>This is your first time logging into Dandelion. Please reset your password:</p>

<br />
<?php echo (isset($_SESSION['errors']) ? $_SESSION['errors'] : ''); $_SESSION['errors']=''; ?>
<br />

<div id="editform">
	<form name="edit_form" method="post" action="lib/resetpw.php">
		<table>
			<tr><td>New Password:</td><td><input type="password" name="reset_1" /></td></tr>
			<tr><td>Repeat Password:</td><td><input type="password" name="reset_2" /></td></tr>
		</table>
		<input type="submit" name="sub_act" value="Reset" />
	</form>
</div>
<!-- End Page Body -->  
<?php include 'static/includes/footer.php'; ?>
