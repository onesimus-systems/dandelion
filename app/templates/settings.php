<?php
/**
 * User settings page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['jqueryui']]);
?>
<!-- Begin Page Body -->
<h2>User Settings</h2>

<div id="dialogBox">
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
    <input type="text" id="show_limit" size="3" value="<?= $_SESSION['userInfo']['showlimit'] ?>">
    <button type="button" class="dButton" onClick="api.saveLogLimit();">Save Limit</button>
</form>

<br><hr width="350"><br>

<form>
	Current theme:

	<?= $this->getThemeList() ?>

    <button type="button" class="dButton" onClick="api.saveTheme();">Save Theme</button>
</form>

<?php if ($publicApiEnabled): ?>
    <br><hr width="350"><br>

    <form>
        <span id="apiKey"></span>
        <br><button type="button" class="dButton" onClick="api.generateKey();">Regenerate Key</button>
    </form>
<?php
endif;

echo $this->loadJS(['jquery','jqueryui','common','settings']); ?>
<!-- End Page Body -->
