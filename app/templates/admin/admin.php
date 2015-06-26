<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['admin','jqueryui']]);
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
            </tr>

            <?php
            foreach ($userlist as $user) {
                echo '<tr onClick="Admin.editUser('.$user['id'].')">';
                echo '<td>'.$user['fullname'].'</td>';
                echo '<td>'.$user['username'].'</td>';
                echo '<td>'.$grouplist[$user['group_id']]['name'].'</td>';
                echo '<td class="non-essential-info">'.$user['created'].'</td>';
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
                echo '<tr onClick="Admin.editGroup(\''.$id.'\')">';
                echo '<td>'.$group['name'].'</td>';
                echo '<td>'.implode(', ', $group['users']).'</td>';
                echo '</tr>';
            } ?>
        </table>
    </div>
</section>
<?php endif;

if ($catList): ?>
<section id="category-mgt">
    <h2>Category Management</h2>

    <div class="admin-div">
        <button type="button" id="add-category-button" class="button">Add Category</button>
        <button type="button" id="edit-category-button" class="button">Edit Category</button>
        <button type="button" id="delete-category-button" class="button">Delete Category</button>

        <div id="categories">Loading Categories...</div>
    </div>
</section>
<?php endif;

if ($showUpdateSection): ?>
<section id="version-update">
	<h2>Updates</h2>
	<?php if (!$updates): ?>
		No updates currently available
	<?php else: ?>
		<strong>An update is available!</strong><br><br>
		<strong>Installed Version</strong>: <?= $this->e($updates['current']) ?><br><br>
		<strong>Latest Version</strong>: <?= $this->e($updates['latest']) ?><br><br>
		<a href="<?= $this->e($updates['url']) ?>">Download Update</a>
	<?php endif; ?>
</section>
<?php endif;

echo $this->loadJS(['jquery', 'jqueryui', 'common', 'categories', 'admin']);
?>
