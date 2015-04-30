<?php
/**
 * User management page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['edituser','jqueryui','datetimepicker']]);
?>

<h1>Manage User - <?= $this->e($user->userInfo['realname']) ?></h1>

<form>
<div id="pwd-reset-dialog" title="Reset Password">
    <table>
        <tr><td>New Password:</td><td><input type="password" id="pass1"></td></tr>
        <tr><td>Repeat Password:</td><td><input type="password" id="pass2"></td></tr>
    </table>
</div>

<div id="control-panel">
    <button type="button" id="delete-user-btn">Delete User</button>
    <button type="button" id="reset-pwd-btn">Reset Password</button>
    <button type="button" id="revoke-api-btn">Revoke API Key</button>
    <button type="button" id="save-btn">Save User</button>
    <span id="message"></span>
</div>

<section id="general-info">
    <h2>General Information</h2>
    <input type="hidden" id="user-id" value="<?= $this->e($user->userInfo['userid']) ?>">
    <table>
        <tr>
            <td class="field-name">Username:</td>
            <td><?= $this->e($user->userInfo['username']) ?></td>
        </tr>
        <tr>
            <td class="field-name">Full Name:</td>
            <td><input type="text" id="fullname" value="<?= $this->e($user->userInfo['realname']) ?>"></td>
        </tr>
        <tr>
            <td class="field-name">Group:</td>
            <td>
                <select id="user-group">
                <?php
                    foreach ($grouplist as $group) {
                        if ($user->userInfo['role'] == $group['role']) {
                            echo '<option value="'.$group['role'].'" selected>'.ucfirst($group['role']).'</option>';
                        } else {
                            echo '<option value="'.$group['role'].'">'.ucfirst($group['role']).'</option>';
                        }
                    }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="field-name">Date Created:</td>
            <td><?= $this->e($user->userInfo['datecreated']) ?></td>
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
                        if ($user->userCheesto['status'] == $status) {
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
                <textarea id="user-status-message"><?= $this->e($user->userCheesto['message']) ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="field-name">Return Time:</td>
            <td><input type="text" id="user-status-return" value="<?= $this->e($user->userCheesto['returntime']) ?>"></td>
        </tr>
    </table>
</section>
</form>

<?= $this->loadJS(['jquery', 'jqueryui', 'timepicker', 'common', 'usermanager']) ?>
