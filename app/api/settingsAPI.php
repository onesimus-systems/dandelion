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
namespace Dandelion\API;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(ApiController::makeDAPI(2, 'This script can only be called by the API.', 'settings'));
}

class settingsAPI
{
    public static function saveLogLimit($db, $ur, $params) {
        $settings = new \Dandelion\userSettings($db);
        return json_encode($settings->saveLogLimit($params->limit));
    }

    public static function saveTheme($db, $ur, $params) {
        $settings = new \Dandelion\userSettings($db);
        return json_encode($settings->saveTheme($params->theme));
    }

    public static function getThemeList($db, $ur, $params) {
        return json_encode(\Dandelion\getThemeListArray());
    }
}
