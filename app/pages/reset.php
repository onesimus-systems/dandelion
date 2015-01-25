<?php
/**
 * This page is seen by a person once after their account
 * is initially created. It updates their record with an Bcrypt
 * encrypted password and changes firsttime to 0.
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

$requiredCssFiles = array();
include ROOT.'/pages/includes/head.php';
?>
<!-- Begin Page Body -->
<p>This is your first time logging into Dandelion. Please reset your password:</p>

<br>
<?php echo (isset($_SESSION['errors']) ? $_SESSION['errors'] : ''); $_SESSION['errors']=''; ?>
<br>

<div id="passwordResetDialog">
    <h2>Reset Password for <?php echo $_SESSION['userInfo']['realname']; ?>:</h2>
    <form>
        <table>
            <tr><td>New Password:</td><td><input type="password" id="pass1"></td></tr>
            <tr><td>Repeat Password:</td><td><input type="password" id="pass2"></td></tr>
            <tr><td></td><td><button type="button" onClick="api.resetPassword();">Reset Password</button></tr>
        </table>
    </form>
</div>

<?php echo loadJS('jquery','reset'); ?>
<!-- End Page Body -->
<?php include ROOT.'/pages/includes/footer.php'; ?>
