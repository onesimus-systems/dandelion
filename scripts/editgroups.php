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
namespace Dandelion;

require_once 'bootstrap.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = $_GET['action'];
    $permissions = new Permissions();

    if ($action == 'getlist') {
        $list = $permissions->getGroupList();

        echo '<select id="groupList">';
        echo '<option value="0">Select:</option>';
        foreach ($list as $group) {
            echo '<option value="'.$group['id'].'">'.ucfirst($group['role']).'</option>';
        }
        echo '</select>';
    } elseif ($action == 'getpermissions') {
        $gid = $_GET['groups'];
        $groupPermissions = $permissions->getGroupList($gid)[0];

        echo json_encode(unserialize($groupPermissions['permissions']));
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $permissions = new Permissions();

    if ($action == 'save' && $_SESSION['rights']['editgroup']) {
        $newPermissions = json_decode($_POST['permissions']);
        $gid = $_POST['gid'];

        if ($permissions->editGroup($gid, $newPermissions)) {
            echo 'Permissions saved successfully';
        } else {
            echo 'An error occured';
        }
    } elseif ($action == 'create' && $_SESSION['rights']['addgroup']) {
        $name = $_POST['name'];
        $rights = (array) json_decode($_POST['rights']);
        echo $permissions->createGroup($name, $rights);
    } elseif ($action == 'delete' && $_SESSION['rights']['deletegroup']) {
        $gid = $_POST['groups'];

        $users = $permissions->usersInGroup($gid);

        if ($users[0]) {
            echo 'This group is assigned to users.<br>Can not delete this group.';
        } else {
            $permissions->deleteGroup($gid);
            echo 'Group deleted successfully.';
        }
    }
}
