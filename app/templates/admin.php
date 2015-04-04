<?php
/**
 * Administration dashboard
 */
// Stays false unless an admin section is shown
$content = false;
$this->layout('layouts::main', ['requiredCssFiles' => ['admin','jqueryui']]);
?>

<h1>Administration</h1>

<?php if ($userRights->authorized(array('createuser', 'edituser', 'deleteuser'))):
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
                <th class="non-essential-info">Theme</th>
                <th class="non-essential-info">First Login</th>
            </tr>

            <?php
            foreach ($userlist as $user) {
                echo '<tr onClick="Admin.editUser('.$user['userid'].')">';
                echo '<td>'.$user['realname'].'</td>';
                echo '<td>'.$user['username'].'</td>';
                echo '<td>'.$user['role'].'</td>';
                echo '<td class="non-essential-info">'.$user['datecreated'].'</td>';
                echo '<td class="non-essential-info">'.$user['theme'].'</td>';
                echo '<td class="non-essential-info">'.$user['firsttime'].'</td>';
                echo '</tr>';
            } ?>
        </table>
    </div>
</section>
<?php endif;

if ($userRights->authorized(array('creategroup', 'editgroup', 'deletegroup'))):
	$content = true; ?>
<!-- <section id="group-mgt">
    <h2>Role Management</h2>

    <div class="admin-div">
        <button type="button" id="add-role-button" class="button">Add Role</button>
        <button type="button" id="edit-role-button" class="button">Edit Role</button>
        <button type="button" id="delete-role-button" class="button">Delete Role</button>

        <table id="roles-table">
            <tr>
                <th>Role</th>
                <th>Users in role</th>
            </tr>

            <tr id="role1" onClick="Admin.highlightRole(1);">
                <td>admin</td>
                <td>dragonrider23, admin</td>
            </tr>

            <tr id="role2" onClick="Admin.highlightRole(2);">
                <td>user</td>
                <td>No users</td>
            </tr>

            <tr id="role3" onClick="Admin.highlightRole(3);">
                <td>guest</td>
                <td>No users</td>
            </tr>
        </table>
    </div>
</section> -->
<?php endif;

if ($userRights->authorized(array('createcat', 'editcat', 'deletecat'))):
	$content = true; ?>
<!-- <section id="category-mgt">
    <h2>Category Management</h2>

    <div class="admin-div">
        <button type="button" id="add-category-button" class="button">Add Category</button>
        <button type="button" id="edit-category-button" class="button">Edit Category</button>
        <button type="button" id="delete-catergory-button" class="button">Delete Category</button>

        <div id="category-selects">
            <select id="level-1">
                <option>Select:</option>
                <option>Root 1</option>
                <option>Root 2</option>
            </select>

            <select id="level-2">
                <option>Select:</option>
                <option>Sub 1</option>
                <option>Sub 2</option>
            </select>

            <select id="level-3">
                <option>Select:</option>
                <option>Sub 1</option>
                <option>Sub 2</option>
            </select>

            <select id="level-4">
                <option>Select:</option>
                <option>Sub 1</option>
                <option>Sub 2</option>
            </select>

            <select id="level-5">
                <option>Select:</option>
                <option>Sub 1</option>
                <option>Sub 2</option>
            </select>
        </div>
    </div>
</section> -->
<?php endif;

if (!$content) {
	echo 'Your account doesn\'t have rights to administrative controls.';
}

echo $this->loadJS(['jquery', 'jqueryui', 'common', 'admin']);
?>
<script type="text/javascript">
    Admin.init();
</script>
