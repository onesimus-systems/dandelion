<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['usersettings', 'jqueryui'], 'logoutOnly' => true]);
?>
<!-- Begin Page Body -->

<h4>Since this is your first time logging into Dandelion, please reset your password:</h4>

<form class="password-reset-form">
    <fieldset>
        <label for="current-password">New Password:</label>
        <div class="textfield"><input type="password" id="new-password-1" autofocus></div>
    </fieldset>
    <fieldset>
        <label for="current-password">Repeat New Password:</label>
        <div class="textfield"><input type="password" id="new-password-2"></div>
    </fieldset>
    <fieldset class="submit-btn">
        <button type="button" id="reset-password-btn" class="button">Reset Password</button>
    </fieldset>
</form>

<script type="text/javascript">
    var page = 'initialReset';
</script>

<?= $this->loadJS(['jquery', 'jqueryui', 'settings']) ?>
<!-- End Page Body -->
