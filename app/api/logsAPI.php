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
    exit(ApiController::makeDAPI(2, 'This script can only be called by the API.', 'logs'));
}

class logsAPI
{
    /**
     * Grab JSON array of logs
     *
     * @return JSON
     */
    public static function read($db, $ur) {
        if (!$ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $userLimit = new \Dandelion\userSettings($db);
        $limit = (int) $userLimit->getSetting('showlimit', USER_ID);
        $limit = isset($_REQUEST['limit']) ? (int) $_REQUEST['limit'] : $limit;
        $offset = isset($_REQUEST['offset']) ? (int) $_REQUEST['offset'] : 0;
        $offset = $offset < 0 ? 0 : $offset;

        $logSize = (int) $db->numOfRows('log');

        if ($offset > $logSize) {
            $offset = $offset - $limit;
        }

        $metaData = array(
            'offset' => $offset,
            'limit'  => $limit,
            'logSize' => $logSize
        );

        $logs = new \Dandelion\logs($db, $ur);
        $return = (array) json_decode($logs->getJSON($limit, $offset));
        $return['metadata'] = $metaData;
        return json_encode($return);
    }

    /**
     * Get data for a single log
     */
    public static function readOne($db, $ur) {
        if (!$ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $logs = new \Dandelion\logs($db);
        return $logs->getLogInfo($_REQUEST['logid']);
    }

    public static function create($db, $ur) {
        if (!$ur->authorized('createlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
        $body = isset($_REQUEST['body']) ? $_REQUEST['body'] : '';
        $cat = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : NULL;

        $title = urldecode($title);
        $body = urldecode($body);
        $cat = rtrim(urldecode($cat), ':');

        $logs = new \Dandelion\logs($db);
        return json_encode($logs->addLog($title, $body, $cat, USER_ID));
    }

    public static function edit($db, $ur) {
        $lid = isset($_REQUEST['logid']) ? $_REQUEST['logid'] : '';

        if (!$ur->isAdmin()) {
            $log = (array) json_decode(self::readOne($db, $ur));

            if (!$ur->authorized('editlog') || $log['usercreated'] != USER_ID) {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
            }
        }

        $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
        $body = isset($_REQUEST['body']) ? $_REQUEST['body'] : '';
        //$cat = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : NULL;

        $title = urldecode($title);
        $body = urldecode($body);
        //$cat = rtrim(urldecode($cat), ':');

        $logs = new \Dandelion\logs($db);
        return json_encode($logs->editLog($lid, $title, $body));
    }

    public static function filter($db, $ur) {
        if (!$ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
        $filter = urldecode($filter);
        $filter = rtrim($filter, ':');

        $logs = new \Dandelion\logs($db);
        return $logs->filter($filter);
    }

    public static function search($db, $ur) {
        if (!$ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $kw = isset($_REQUEST['kw']) ? $_REQUEST['kw'] : '';
        $kw = urldecode($kw);
        $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
        $date = urldecode($date);

        $logs = new \Dandelion\logs($db);
        return $logs->search($kw, $date);
    }
}
