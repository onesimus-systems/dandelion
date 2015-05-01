<?php
/**
 * User settings page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['user-settings', 'jqueryui']]);
?>
<!-- Begin Page Body -->
<h1>User Settings</h1>

<h2>Preferences</h2>

<form>
    <fieldset>
        <label for="page-limit">Logs shown per page:</label>
        <div class="textfield"><input type="text" id="page-limit" size="5" maxlength="3" value="<?= $_SESSION['userInfo']['showlimit'] ?>"></div>

        <button type="button" id="save-per-page-btn" class="button">Save</button>
    </fieldset>

    <fieldset>
        <label for="theme">Theme:</label>
        <?= $this->getThemeList() ?>

        <button type="button" id="save-theme-btn" class="button">Save</button>
    </fieldset>
</form>

<h2>Reset Password</h2>

<form class="password-reset-form">
    <fieldset>
        <label for="current-password">New Password:</label>
        <div class="textfield"><input type="password" id="new-password-1"></div>
    </fieldset>
    <fieldset>
        <label for="current-password">Repeat New Password:</label>
        <div class="textfield"><input type="password" id="new-password-2"></div>
    </fieldset>
    <fieldset class="submit-btn">
        <button type="button" id="reset-password-btn" class="button">Reset Password</button>
    </fieldset>
</form>

<?php if ($publicApiEnabled): ?>
    <h2>Public API</h2>

    <form>
        <span id="apikey"><strong>Key:</strong> <?= $this->e($apiKey) ?></span>
        <button type="button" id="generate-apikey-btn" class="button">Regenerate Key</button>
    </form>
<?php
endif;

echo $this->loadJS(['jquery', 'jqueryui', 'common', 'settings']); ?>
<!-- End Page Body -->
