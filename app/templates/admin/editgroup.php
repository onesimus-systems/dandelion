<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['css' => ['editgroup','jqueryui']]);
?>

<h1>Manage Group - <?= $this->e($group['name']) ?></h1>

<form>

<section id="control-panel">
    <button type="button" id="save-btn">Save Group</button>
    <button type="button" id="delete-btn">Delete Group</button>
    <span id="message"></span>
</section>

<section class="permissions">
    <input type="hidden" id="groupid" value="<?= $this->e($group['id']) ?>">

    <table>
        <tr>
            <th>Permission</th>
            <th>Can do?</th>
        </tr>
        <?php
        foreach ($group['permissions'] as $pname => $pvalue) {
            echo '<tr class="permission">';
            echo '<td>'.$group['permissionNames'][$pname].'</td>';
            if ($pvalue) {
                echo '<td><input type="checkbox" id="'.$pname.'" value="'.$pname.'" checked></td>';
            } else {
                echo '<td><input type="checkbox" id="'.$pname.'" value="'.$pname.'"></td>';
            }
            echo '</tr>';
        }
        ?>
    </table>
</section>

</form>

<section class="users-in-group">
    <h3>Users in this group:</h3>
    <ul>
        <?php
        if (!count($usersInGroup)) {
            echo '<li>No users in this group</li>';
        } else {
            foreach ($usersInGroup as $user) {
                echo "<li>{$user['fullname']} - {$user['username']}</li>";
            }
        }
        ?>
    </ul>
</section>

<?= $this->loadJS(['jquery', 'jqueryui', 'groupmanager']) ?>
