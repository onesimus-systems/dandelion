<?php
/**
  * Save and manage user settings.
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
  * @date Jan 2015
***/
namespace Dandelion;

use \Dandelion\Storage\Contracts\DatabaseConn;

class userSettings {
    public function __construct(DatabaseConn $db) {
        $this->db = $db;
    }

    public function saveLogLimit($limit) {
        if ($limit < 5 || $limit > 500) {
            return 'Please choose a number between 5 and 500.';
        }

        $this->db->update(DB_PREFIX.'users')
                 ->set('showlimit = :newlimit')
                 ->where('userid = :myID');
        $params = array(
            'newlimit' => $limit,
            'myID' => $_SESSION['userInfo']['userid']
        );
        $this->db->go($params);

        $_SESSION['userInfo']['showlimit'] = $limit;

        return 'Show limit changed successfully';
    }

    public function saveTheme($theme) {
        $newTheme = isset($theme) ? $theme : 'default';

        $this->db->update(DB_PREFIX.'users')
                 ->set('theme = :theme')
                 ->where('userid = :myID');
        $params = array(
            'theme' => $newTheme,
            'myID' => $_SESSION['userInfo']['userid']
        );
        $this->db->go($params);

        $_SESSION['userInfo']['theme'] = $newTheme;

        return 'Theme saved successfully';
    }

    public function getSetting($setting, $id) {
        $this->db->select($setting)
                 ->from(DB_PREFIX.'users')
                 ->where('userid = :id');

        $params = array('id' => $id);
        return $this->db->get($params)[0][$setting];
    }
}
