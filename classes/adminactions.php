<?php
/**
 * This class contains any non-user related admin functions.
 *
 * @author Lee Keitel
 * @date May, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

class adminActions
{
    public static function doAction($data) {
        $conn = new dbManage();
        return SELF::$data['action']($conn, $data['data']);
    }

	private static function saveSlogan($conn, $data) {
		// Set new slogan
		$stmt = 'UPDATE `'.DB_PREFIX.'settings` SET `value` = :slogan WHERE `name` = "slogan"';
		$params = array(
			'slogan' => urldecode($data)
		);
		$conn->queryDB($stmt, $params);
		
		$_SESSION['app_settings']['slogan'] = urldecode($data);
		
		return 'Slogan set successfully';
	}
	
	private static function backupDB($conn) {
		backupDB::doBackup($conn);
	}
	
	private static function saveDefaultTheme($conn, $data) {
		// Set new default theme
		$stmt = 'UPDATE `'.DB_PREFIX.'settings` SET `value` = :theme WHERE `name` = "default_theme"';
		$params = array(
			'theme' => $data		
		);
		$conn->queryDB($stmt, $params);
		
		$_SESSION['app_settings']['default_theme'] = $data;
		
		return 'Default theme set successfully';
	}
	
	private static function saveCheesto($conn, $data) {
		// Set cheesto enabled/disabled
		$stmt = 'UPDATE `'.DB_PREFIX.'settings` SET `value` = :enabled WHERE `name` = "cheesto_enabled"';
		$enabled = ($data == 'true') ? 1 : 0;
		$params = array(
			'enabled' => $enabled
		);
		$conn->queryDB($stmt, $params);
		
		$_SESSION['app_settings']['cheesto_enabled'] = $enabled;
		
		return 'Settings set successfully';
	}
}