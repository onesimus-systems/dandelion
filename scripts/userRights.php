<?php
/**
 * Handles user rights
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date March 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

function userLinks() {
	global $admin_link;
	global $settings_link;
	
	if ($_SESSION['userInfo']['role'] === "admin") {
		$admin_link = '<a href="admin.phtml">Administration</a>';
	}
	else {
		$admin_link = '';
	}
	
	if ($_SESSION['userInfo']['role'] !== "guest") {
		$settings_link = '<a href="settings.phtml">Settings</a>';
	}
	else {
		$settings_link = '';
	}
}