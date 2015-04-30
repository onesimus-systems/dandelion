<?php
/**
 * Administration dashboard
 */
// Stays false unless an admin section is shown
$content = false;
$this->layout('layouts::main', ['requiredCssFiles' => ['admin','jqueryui']]);
?>

<h1>Administration</h1>

<?php if ($userlist):
	$content = true; ?>
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
                echo '<tr onClick="Admin.editUser('.$user['userid'].')">';
                echo '<td>'.$user['realname'].'</td>';
                echo '<td>'.$user['username'].'</td>';
                echo '<td>'.$user['role'].'</td>';
                echo '<td class="non-essential-info">'.$user['datecreated'].'</td>';
                echo '</tr>';
            } ?>
        </table>
    </div>
</section>
<?php endif;

if ($grouplist):
	$content = true; ?>
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
            foreach ($grouplist as $group) {
                echo '<tr onClick="Admin.editGroup(\''.$group['role'].'\')">';
                echo '<td>'.$group['role'].'</td>';
                echo '<td>'.implode(', ', $group['users']).'</td>';
                echo '</tr>';
            } ?>
        </table>
    </div>
</section>
<?php endif;

if ($catList):
	$content = true; ?>
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

if (!$content) {
	echo 'Your account doesn\'t have rights to administrative controls.';
}

echo $this->loadJS(['jquery', 'jqueryui', 'common', 'categories', 'admin']);
?>
