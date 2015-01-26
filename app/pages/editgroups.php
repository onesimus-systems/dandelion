<?php
/**
 * Rights group management page
 */
namespace Dandelion;

use \Dandelion\Utils\View;

$requiredCssFiles = array("jqueryui", "permissions.css");
include $paths['app'].'/pages/includes/head.php';
?>
<!-- Begin Page Body -->
<div id="dialog" title="Alert"></div>
<div id="add-form" title="Create Rights Group" style="display: none;">
    <form>
        <fieldset style="border: none;">
            <label for="name">Group Name:</label>
            <input type="text" name="name" id="name" onKeyPress="permissions.check(event);" class="text ui-widget-content ui-corner-all" autocomplete="off">
        </fieldset>
    </form>
</div>

<h2>Group Management</h2>

<div id="groups"></div>

<form>
    <div name="categorySelects" id="categorySelects"></div><br>

    <?php
    if ($User_Rights->authorized('addgroup')) {
        echo '<input type="button" class="dButton" onClick="permissions.createNew();" value="Add Group">';
    }

    if ($User_Rights->authorized('editgroup')) {
        echo '<input type="button" class="dButton" onClick="permissions.getPermissions();" value="Edit Group">';
    }

    if ($User_Rights->authorized('deletegroup')) {
        echo '<input type="button" class="dButton" onClick="permissions.deleteGroup();" value="Delete Group">';
    }
    ?>
</form>

<div id="permissionsBlock">
    <form id="permissionsForm">
        <table>
            <tr class="topRoom">
                <td colspan="3">Logs:</td>
            </tr>
            <tr>
                <td>Create: <input type="checkbox" id="createlog" onChange="permissions.checkGrid('createlog');"></td>
                <td>Edit: <input type="checkbox" id="editlog" onChange="permissions.checkGrid('editlog');"></td>
                <td>View: <input type="checkbox" id="viewlog"></td>
            </tr>

            <tr class="topRoom">
                <td colspan="3">Categories:</td>
            </tr>
            <tr>
                <td>Create: <input type="checkbox" id="addcat"></td>
                <td>Edit: <input type="checkbox" id="editcat"></td>
                <td>Delete: <input type="checkbox" id="deletecat"></td>
            </tr>

            <tr class="topRoom">
                <td colspan="3">Users:</td>
            </tr>
            <tr>
                <td>Create: <input type="checkbox" id="adduser"></td>
                <td>Edit: <input type="checkbox" id="edituser"></td>
                <td>Delete: <input type="checkbox" id="deleteuser"></td>
            </tr>

            <tr class="topRoom">
                <td colspan="3">Groups:</td>
            </tr>
            <tr>
                <td>Create: <input type="checkbox" id="addgroup"></td>
                <td>Edit: <input type="checkbox" id="editgroup"></td>
                <td>Delete: <input type="checkbox" id="deletegroup"></td>
            </tr>

            <tr class="topRoom">
                <td colspan="3">&#264;eesto:</td>
            </tr>
            <tr>
                <td>View: <input type="checkbox" id="viewcheesto"></td>
                <td>Update: <input type="checkbox" id="updatecheesto" onChange="permissions.checkGrid('updatecheesto');"></td>
                <td>&nbsp;</td>
            </tr>
            <tr class="topRoom">
                <td colspan="3">Admin: <input type="checkbox" id="admin" onChange="permissions.checkGrid('admin');"></td>
            </tr>
        </table>

        <input type="button" value="Revert Changes" onClick="permissions.goBack();">
        <input type="button" value="Save Permissions" onClick="permissions.savePermissions();">
    </form>
</div>

<?php echo View::loadJS('permissions','jquery','jqueryui'); ?>
<script type="text/javascript">
    permissions.getList();
</script>
<!-- End Page Body -->
<?php include $paths['app'].'/pages/includes/footer.php'; ?>
