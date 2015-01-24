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

class logsAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    /**
     * Grab JSON array of logs
     *
     * @return JSON
     */
    public function read() {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $userLimit = new \Dandelion\userSettings($this->db);
        $limit = $userLimit->getSetting('showlimit', USER_ID);
        $limit = (int) $this->up->get('limit', $limit);
        $offset = (int) $this->up->get('offset', 0);
        $offset = $offset < 0 ? 0 : $offset;

        $logSize = (int) $this->db->numOfRows('log');

        if ($offset > $logSize) {
            $offset = $offset - $limit;
        }

        $metaData = array(
            'offset' => $offset,
            'limit'  => $limit,
            'logSize' => $logSize
        );

        $logs = new \Dandelion\logs($this->db, $this->ur);
        $return = (array) json_decode($logs->getJSON($limit, $offset));
        $return['metadata'] = $metaData;
        return json_encode($return);
    }

    /**
     * Get data for a single log
     */
    public function readOne() {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $logs = new \Dandelion\logs($this->db);
        return $logs->getLogInfo($this->up->logid);
    }

    public function create() {
        if (!$this->ur->authorized('createlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $title = $this->up->title;
        $body = $this->up->body;
        $cat = $this->up->cat;

        $cat = rtrim($cat, ':');

        $logs = new \Dandelion\logs($this->db);
        return json_encode($logs->addLog($title, $body, $cat, USER_ID));
    }

    public function edit() {
        $lid = $this->up->logid;

        if (!$this->ur->isAdmin()) {
            $log = (array) json_decode(self::readOne($this->db, $this->ur));

            if (!$this->ur->authorized('editlog') || $log['usercreated'] != USER_ID) {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
            }
        }

        $title = $this->up->title;
        $body = $this->up->body;
        //$cat = $this->up->cat;

        //$cat = rtrim($cat, ':');

        $logs = new \Dandelion\logs($this->db);
        return json_encode($logs->editLog($lid, $title, $body));
    }

    public function filter() {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $filter = $this->up->filter;
        $filter = rtrim($filter, ':');

        $logs = new \Dandelion\logs($this->db);
        return $logs->filter($filter);
    }

    public function search() {
        if (!$this->ur->authorized('viewlog')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'logs'));
        }

        $kw = $this->up->kw;
        $date = $this->up->date;

        $logs = new \Dandelion\logs($this->db);
        return $logs->search($kw, $date);
    }
}
