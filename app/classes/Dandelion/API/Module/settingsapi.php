<?php
/**
 * Handles API requests for Logs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date July 2014
 */
namespace Dandelion\API\Module;

use Dandelion\Controllers\ApiController;

class settingsAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    public function saveLogLimit() {
        $settings = new \Dandelion\userSettings($this->db);
        return json_encode($settings->saveLogLimit($this->up->limit));
    }

    public function saveTheme() {
        $settings = new \Dandelion\userSettings($this->db);
        return json_encode($settings->saveTheme($this->up->theme));
    }

    public function getThemeList() {
        return json_encode(\Dandelion\getThemeListArray());
    }
}
