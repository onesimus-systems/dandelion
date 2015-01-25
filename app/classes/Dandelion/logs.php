<?php
/**
 * Handles all requests pertaining to log entries
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
 * @date December 2014
 */
namespace Dandelion;

class logs {
    public function __construct(DatabaseConn $dbConn, $ur = null) {
        $this->db = $dbConn;
        $this->ur = $ur;
        return;
    }

    /**
     * Get log data from database
     *
     * @param int $logid Row id number for desired log
     *
     * @return JSON encoded array with log data
     */
    public function getLogInfo($logid) {
        $logid = isset($logid) ? $logid : '';

        $this->db->select()
                 ->from(DB_PREFIX.'log')
                 ->where('logid = :logid');
        $params = array(
            'logid' => $logid
        );
        $edit_log_info = $this->db->get($params);

        return json_encode($edit_log_info[0]);
    }

    /**
     * Get JSON of log list showing $limit number of logs
     *
     * @param int $limit - Number of logs to get
     * @param int $offset - Offset for pagination
     */
    public function getJSON($limit = 25, $offset = 0) {
        $this->db->select('l.*, u.realname')
                 ->from(DB_PREFIX.'log AS l LEFT JOIN '.DB_PREFIX.'users AS u ON l.usercreated = u.userid')
                 ->orderBy('l.logid', 'DESC')
                 ->limit(':pO,:lim');

        $params = array(
            'pO' => ((int) $offset),
            'lim' => ((int) $limit)
        );

        // When using an SQL LIMIT, the parameter MUST be an integer.
        // To accomplish this the PDO constant PARAM_INT is passed
        $get_logs = $this->db->get($params, \PDO::PARAM_INT);

        foreach ($get_logs as $key => $value) {
            if ($this->ur->isAdmin() || $value['usercreated'] == $this->ur->userid) {
                $get_logs[$key]['canEdit'] = true;
            } else {
                $get_logs[$key]['canEdit'] = false;
            }
        }
        return json_encode($get_logs);
    }

    /**
     * Create a new log in the database
     *
     * @param string $title
     * @param string $body
     * @param string $cat
     * @param int $uid - User ID
     *
     * @return string Confirmation message or error message
     */
    public function addLog($title, $body, $cat, $uid) {
        if (empty($title) || empty($body) || empty($cat) || $cat == 'Select:') {
            return 'Log entries require a title, category, and body.';
        }

        $datetime = getdate();
        $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
        $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];

        $this->db->insert()
                 ->into(DB_PREFIX.'log', array('datec', 'timec', 'title', 'entry', 'usercreated', 'cat'))
                 ->values(array(':datec', ':timec', ':title', ':entry', ':usercreated', ':cat'));
        $params = array(
            'datec' => $new_date,
            'timec' => $new_time,
            'title' => $title,
            'entry' => $body,
            'usercreated' => $uid,
            'cat' => $cat
        );
        if ($this->db->go($params)) {
            return 'Log entry created successfully.';
        } else {
            return 'An error occured saving the log entry.';
        }
    }

    /**
     * Update log in database
     *
     * @param json $logData JSON encoded log title, entry, and id
     *
     * @return string Confirmation message or error message
     */
    public function editLog($lid, $title, $body) {
        if (empty($body) || empty($title) || empty($lid)) {
            return 'Log entries require a title, category, and body.';
        }

        $this->db->update(DB_PREFIX.'log')
                 ->set('title = :title, entry = :entry, edited = 1')
                 ->where('logid = :lid');
        $params = array(
            'title' => $title,
            'entry' => $body,
            'lid' => $lid
        );
        $this->db->go($params);

        return "\"{$title}\" edited successfully.";
    }

    /**
     * Filter logs by category
     */
    public function filter($f) {
        $this->db->select('l.*, u.realname')
                 ->from(DB_PREFIX.'log AS l LEFT JOIN '.DB_PREFIX.'users AS u ON l.usercreated = u.userid')
                 ->where('cat LIKE :filter')
                 ->orderBy('logid', 'DESC');
        $params = array('filter' => "%{$f}%");
        return json_encode($this->db->get($params));
    }

    /**
     * Search logs by title and content
     *
     * @param string $kw - Keywords
     * @param string $date - Date of creation
     *
     * @return Json
     */
    public function search($kw = '', $date = '') {
        $this->db->select('l.*, u.realname')
                 ->from(DB_PREFIX.'log AS l LEFT JOIN '.DB_PREFIX.'users AS u ON l.usercreated = u.userid')
                 ->orderBy('logid', 'DESC');

        if ($date == '') {
            $this->db->where('title LIKE :keyw or entry LIKE :keyw');
            $params = array(
                'keyw' => "%{$kw}%"
            );
        } elseif ($kw == '') {
            $this->db->where('datec=:dates');
            $params = array(
                'dates' => $date
            );
        } else {
            $this->db->where('(title LIKE :keyw or entry LIKE :keyw) and datec=:dates');
            $params = array(
                'keyw' => "%{$kw}%",
                'dates' => $date
            );
        }

        return json_encode($this->db->get($params));
    }
}
