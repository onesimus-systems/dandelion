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
namespace Dandelion;

class adminactions
{
    public function __construct($db) {
        $this->dbConn = $db;
    }

    /**
     * Save the website tagline
     *
     * @param $data string - Website slogan
     * @return string
     */
    public function saveSlogan($data)
    {
        // Save new slogan
        $this->dbConn->update(DB_PREFIX.'settings')
                      ->set('value = :slogan')
                      ->where('name = "slogan"');
        $params = array(
            'slogan' => urldecode($data)
        );
        $this->dbConn->go($params);

        $_SESSION['app_settings']['slogan'] = urldecode($data);

        return 'Slogan set successfully';
    }

    /**
     * Call DB backup function
     *
     * @return string
     */
    public function backupDB()
    {
        $saveMe = new backupDB($this->dbConn);
        return $saveMe->doBackup();
    }

    /**
     * Save the default theme for the site
     *
     * @param $data string - Theme name
     * @return string
     */
    public function saveDefaultTheme($data)
    {
        // Set new default theme
        $this->dbConn->update(DB_PREFIX.'settings')
                      ->set('value = :theme')
                      ->where('name = "default_theme"');
        $params = array(
            'theme' => $data
        );
        $this->dbConn->go($params);

        $_SESSION['app_settings']['default_theme'] = $data;

        return 'Default theme set successfully';
    }

    /**
     * Save Cheesto enabled state
     *
     * @param $data bool - Enabled?
     * @return string
     */
    public function saveCheesto($data)
    {
        // Set cheesto enabled/disabled
        $this->dbConn->update(DB_PREFIX.'settings')
                      ->set('value = :enabled')
                      ->where('name = "cheesto_enabled"');
        $enabled = ($data == 'true') ? 1 : 0;
        $params = array(
            'enabled' => $enabled
        );
        $this->dbConn->go($params);

        $_SESSION['app_settings']['cheesto_enabled'] = $enabled;

        return 'Settings set successfully';
    }

    /**
     * Save public API enabled status
     *
     * @param $data bool - Enabled?
     * @return string
     */
    public function savePAPI($data) {
        // Set Public API enabled/disabled
        $this->dbConn->update(DB_PREFIX.'settings')
                      ->set('value = :enabled')
                      ->where('name = "public_api"');
        $enabled = ($data == 'true') ? 1 : 0;
        $params = array(
            'enabled' => $enabled
        );
        $this->dbConn->go($params);

        $_SESSION['app_settings']['public_api'] = $enabled;

        return 'Setting saved';
    }
}
