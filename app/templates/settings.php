<?php
/**
 * User settings page
 */
namespace Dandelion;

use \Dandelion\Utils\View;

$requiredCssFiles = array("jqueryui");
include $paths['app'].'/templates/includes/head.php';
?>
<!-- Begin Page Body -->
<h2>User Settings</h2>

<div id="passwordResetDialogSettings">
    <h2>Reset Password for <?= $_SESSION['userInfo']['realname']; ?>:</h2>
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
    <input type="text" id="show_limit" size="3" value="<?= $_SESSION['userInfo']['showlimit']; ?>">
    <button type="button" class="dButton" onClick="api.saveLogLimit();">Save Limit</button>
</form>

<br><hr width="350"><br>

<form>
	Current theme:

	<?= View::getThemeList(); ?>

    <button type="button" class="dButton" onClick="api.saveTheme();">Save Theme</button>
</form>

<?php if (PUBLIC_API) { ?>
    <br><hr width="350"><br>

    <form>
        <span id="apiKey"></span>
        <br><br><button type="button" class="dButton" onClick="api.generateKey();">Generate New Key</button>
    </form>
<?php
}

echo View::loadJS('jquery','jqueryui','settings'); ?>
<!-- End Page Body -->

<?php include $paths['app'].'/templates/includes/footer.php'; ?>
