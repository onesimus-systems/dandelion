<?php
/**
 * Page for managing user groups and permissions
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date May 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

include_once 'scripts/grabber.php';

if (authenticated()) {
	userLinks();
}
else {
	header( 'Location: index.php' );
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="images/favicon.ico">
		<?php loadCssSheets('permissions.css'); ?>
		<title>Dandelion Web Log</title>
        <?php loadJS('permissions.js','jquery'); ?>
	</head>
	<body onLoad="permissions.getList();">
        <header>
            <?php include 'scripts/header.php'; ?>
        </header>
        
		<h2>Group Management</h2>
		
		<div id="groups"></div>
		
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
        
        <footer>
            <?php include_once 'scripts/footer.php'; ?>
        </footer>
	</body>
</html>