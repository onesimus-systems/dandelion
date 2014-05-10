<?php
/**
 * Entry poing for group management
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date May 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
 
 require_once 'grabber.php';
 
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = $_GET['action'];
    $permissions = new Permissions();
    
    if ($action == 'getlist') {
        $list = $permissions->getGroupList();
        
        echo '<select id="groupList" onChange="permissions.getPermissions(this.value);">';
        echo '<option value="0">Select:</option>';
        foreach ($list as $group) {
            echo '<option value="'.$group['id'].'">'.ucfirst($group['role']).'</option>';
        }
        echo '</select>';
    }
    
    elseif ($action == 'getpermissions') {
        $gid = $_GET['groups'];
        $groupPermissions = $permissions->getGroupList($gid)[0];
        
        echo json_encode(unserialize($groupPermissions['permissions']));
    }
}

elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
	$action = $_POST['action'];
	$permissions = new Permissions();
	
	if ($action == 'save') {
    	$newPermissions = json_decode($_POST['permissions']);
    	$gid = $_POST['gid'];
    	
    	if ($permissions->editGroup($gid, $newPermissions)) {
    		echo 'Permissions saved successfully';
    	}
    	else {
    		echo 'An error occured';
    	}
    }
}