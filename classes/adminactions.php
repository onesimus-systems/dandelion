<?php
/**
  * Handled any non-user related admin functions.
  * 
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
  * The full GPLv3 license is available in LICENSE.md in the root.
  *
  * @author Lee Keitel
  * @date May 2014
***/

/**
 * Only the doAction() method is publically visiable and it takes
 * in the POST data from a request, determins the action from the
 * data and statically calls that function giving it a database
 * connection class (dbManage), and the 'data' index of the POST
 * array
 */
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
		return backupDB::doBackup($conn);
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