<?php
/**
 * This page allows users to change their password
 * and change the number of logs show on the home page.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

$requiredCssFiles = array("jqueryui");
include 'static/includes/head.php';
?>
<!-- Begin Page Body -->
<h2>User Settings</h2>

<div id="passwordResetDialog">
    <h2>Reset Password for <?php echo $_SESSION['userInfo']['realname']; ?>:</h2>
    <form>
        <table>
            <tr><td>New Password:</td><td><input type="password" id="pass1"></td></tr>
            <tr><td>Repeat Password:</td><td><input type="password" id="pass2"></td></tr>
        </table>
    </form>
</div>

<form>
    <button type="button" class="dButton adminButton" onClick="api.showResetPasswordForm();">Reset Password</button>
    <br><br><hr width="350"><br>

    How many logs do you want to see on the main page:<br />
    <input type="text" id="show_limit" size="3" value="<?php echo $_SESSION['userInfo']['showlimit']; ?>">
    <button type="button" class="dButton" onClick="api.saveLogLimit();">Save Limit</button>
</form>

<br><hr width="350"><br>

<form>
	Current theme:

	<?php echo getThemeList(); ?>

    <button type="button" class="dButton" onClick="api.saveTheme();">Save Theme</button>
</form>

<?php if ($_SESSION['app_settings']['public_api']) { ?>
    <br><hr width="350"><br>

    <form>
        API Key: <span id="apiKey"></span>
        <br><br><button type="button" class="dButton" onClick="api.generateKey();">Generate New Key</button>
    </form>
<?php
}

echo loadJS('jquery','jqueryui','settings'); ?>
<!-- End Page Body -->

<?php include 'static/includes/footer.php'; ?>
