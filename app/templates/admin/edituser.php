<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['edituser','jqueryui','datetimepicker']]);
?>

<h1>Manage User - <?= $this->e($user->get('fullname')) ?></h1>

<form>
<div id="pwd-reset-dialog" title="Reset Password">
    <table>
        <tr><td>New Password:</td><td><input type="password" id="pass1"></td></tr>
        <tr><td>Repeat Password:</td><td><input type="password" id="pass2"></td></tr>
        <tr><td>Force Password Reset:</td><td><input type="checkbox" id="force-reset-chk" checked="true"></td></tr>
    </table>
</div>

<div id="control-panel">
    <?php if ($user->enabled()): ?>
        <button type="button" id="disable-user-btn">Disable User</button>
    <?php else: ?>
        <button type="button" id="enable-user-btn">Enable User</button>
    <?php endif; ?>
    <button type="button" id="delete-user-btn">Delete User</button>
    <button type="button" id="reset-pwd-btn">Reset Password</button>
    <button type="button" id="revoke-api-btn">Revoke API Key</button>
    <button type="button" id="save-btn">Save User</button>
    <span id="message"></span>
</div>

<section id="general-info">
    <h2>General Information</h2>
    <input type="hidden" id="user-id" value="<?= $this->e($user->get('id')) ?>">
    <table>
        <tr>
            <td class="field-name">Username:</td>
            <td><?= $this->e($user->get('username')) ?></td>
        </tr>
        <tr>
            <td class="field-name">Full Name:</td>
            <td><input type="text" id="fullname" value="<?= $this->e($user->get('fullname')) ?>"></td>
        </tr>
        <tr>
            <td class="field-name">Group:</td>
            <td>
                <select id="user-group">
                <?php
                    foreach ($grouplist as $group) {
                        if ($user->get('group_id') == $group['id']) {
                            echo '<option value="'.$group['id'].'" selected>'.ucfirst($group['name']).'</option>';
                        } else {
                            echo '<option value="'.$group['id'].'">'.ucfirst($group['name']).'</option>';
                        }
                    }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="field-name">Date Created:</td>
            <td><?= $this->e($user->get('created')) ?></td>
        </tr>
        <tr>
            <td class="field-name">Disabled:</td>
            <td><?= $user->enabled() ? 'No' : 'Yes' ?></td>
        </tr>
        <tr>
            <td class="field-name">Allow API Access:</td>
            <td>
                <select id="user-api-override">
                    <option value="2"
                        <?= $user->get('api_override') == 2 ? 'selected' : '' ?>>Global</option>
                    <option value="1"
                        <?= $user->get('api_override') == 1 ? 'selected' : '' ?>>Allow</option>
                    <option value="0"
                        <?= $user->get('api_override') == 0 ? 'selected' : '' ?>>Deny</option>
                </select>
            </td>
        </tr>
    </table>
</section>

<section id="cheesto-info">
    <h2>Ĉeesto Information</h2>
    <table>
        <tr>
            <td class="field-name">Status:</td>
            <td>
                <select id="user-status">
                <?php
                    foreach ($statuslist as $status) {
                        if ($cheesto['status'] == $status) {
                            echo '<option value="'.$status.'" selected>'.ucfirst($status).'</option>';
                        } else {
                            echo '<option value="'.$status.'">'.ucfirst($status).'</option>';
                        }
                    }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="field-name">Message:</td>
            <td>
                <textarea id="user-status-message"><?= $this->e($cheesto['message']) ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="field-name">Return Time:</td>
            <td><input type="text" id="user-status-return" value="<?= $this->e($cheesto['returntime']) ?>"></td>
        </tr>
    </table>
</section>
</form>

<?= $this->loadJS(['jquery', 'jqueryui', 'timepicker', 'common', 'usermanager']) ?>
