<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['css' => ['admin','jqueryui']]);
?>

<h1>Administration</h1>

<?php if ($userlist): ?>
<section id="users-mgt">
    <h2>User Management</h2>

    <div class="admin-div">
        <button type="button" id="add-user-btn" class="button">Add User</button>

        <table id="users-table">
            <tr>
                <th>Full Name</th>
                <th>Username</th>
                <th>Role</th>
                <th class="non-essential-info">Created</th>
                <th class="non-essential-info">Disabled</th>
            </tr>

            <?php
            foreach ($userlist as $user) {
                $user['disabled'] = $user['disabled'] ? 'Yes' : 'No';
                echo '<tr data-user-id="'.$this->e($user['id']).'">';
                echo '<td>'.$this->e($user['fullname']).'</td>';
                echo '<td>'.$this->e($user['username']).'</td>';
                echo '<td>'.$this->e($grouplist[$user['group_id']]['name']).'</td>';
                echo '<td class="non-essential-info">'.$this->e($user['created']).'</td>';
                echo '<td class="non-essential-info">'.$user['disabled'].'</td>';
                echo '</tr>';
            } ?>
        </table>
    </div>
</section>
<?php endif;

if ($grouplist): ?>
<section id="group-mgt">
    <h2>Group Management</h2>

    <div class="admin-div">
        <button type="button" id="add-role-button" class="button">Add Group</button>

        <table id="group-table">
            <tr>
                <th>Group</th>
                <th>Users in Group</th>
            </tr>

            <?php
            foreach ($grouplist as $id => $group) {
                echo '<tr data-group-id="'.$this->e($id).'">';
                echo '<td>'.$this->e($group['name']).'</td>';
                echo '<td>'.$this->e(implode(', ', $group['users'])).'</td>';
                echo '</tr>';
            } ?>
        </table>
    </div>
</section>
<?php endif;

if ($catList): ?>
<section id="category-mgt"></section>
<?php endif;

echo $this->loadJS(['jquery', 'jqueryui', 'admin']);
?>
