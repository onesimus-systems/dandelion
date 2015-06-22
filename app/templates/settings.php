<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['usersettings', 'jqueryui']]);
?>
<!-- Begin Page Body -->
<h1>User Settings</h1>

<h2>Preferences</h2>

<form>
    <fieldset>
        <label for="page-limit">Logs shown per page:</label>
        <div class="textfield"><input type="text" id="page-limit" size="5" maxlength="3" value="<?= $this->e($logsPerPage) ?>"></div>

        <button type="button" id="save-per-page-btn" class="button">Save</button>
    </fieldset>

    <fieldset>
        <label for="theme">Theme:</label>
        <?php if ($themeInfo) {
            echo '<select id="theme">';
            foreach ($themeInfo as $theme) {
                if ($theme['selected']) {
                    echo '<option value="'.$theme['slug'].'" selected>'.$theme['name'].'</option>';
                } else {
                    echo '<option value="'.$theme['slug'].'">'.$theme['name'].'</option>';
                }
            }
            echo '</select>';
            echo '<button type="button" id="save-theme-btn" class="button">Save</button>';
        } else {
            echo 'Error loading theme list';
        } ?>
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
