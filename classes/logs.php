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

/**
 * The Logs class contains all functions pertaining to creating
 * editing, filtering and initializtion to display logs.
 *
 * Only the doAction() method is publically visiable and it takes
 * in the POST data from a request, determins the action from the
 * data and calls that function giving it the 'data' index of the
 * POST array
 */
class logs extends Database\dbManage
{
    /**
     * Main function to perform action
     *
     * @param array $data POST data array
     *
     * @return Individual function returns
     */
    public function doAction($data)
    {
        return $this->$data['action']($data['data']);
    }

    /**
     * Get log data from database
     *
     * @param int $logid Row id number for desired log
     *
     * @return JSON encoded array with log data
     */
    private function getLogInfo($logid)
    {
        $logid = isset($logid) ? $logid : '';

        $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `logid` = :logid';
        $params = array(
            'logid' => $logid
        );

        $edit_log_info = $this->queryDB($stmt, $params);

        return json_encode($edit_log_info[0]);
    }

    /**
     * Create a new log in the database
     *
     * @param json $logData JSON encoded log title, entry, and category (strings)
     *
     * @return string Confirmation message or error message
     */
    private function addLog($logData)
    {
        if ($_SESSION['rights']['createlog']) {
            $logData = (array) json_decode($logData);

            $new_title = isset($logData['add_title']) ? $logData['add_title'] : '';
            $new_entry = isset($logData['add_entry']) ? $logData['add_entry'] : '';
            $new_category = isset($logData['cat']) ? $logData['cat'] : NULL;

            // Check that all required fields have been entered
            if (!empty($new_title) && !empty($new_entry) && !empty($new_category) && $new_category != 'Select:') {
                $datetime = getdate();
                $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
                $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];

                $new_category = rtrim(urldecode($new_category), ':');
                $new_title = urldecode($new_title);
                $new_entry = urldecode($new_entry);

                // Add new entry
                $stmt = 'INSERT INTO `'.DB_PREFIX.'log` (datec, timec, title, entry, usercreated, cat)  VALUES (:datec, :timec, :title, :entry, :usercreated, :cat)';
                $params = array(
                    'datec' => $new_date,
                    'timec' => $new_time,
                    'title' => $new_title,
                    'entry' => $new_entry,
                    'usercreated' => $_SESSION['userInfo']['userid'],
                    'cat' => $new_category,
                );
                $this->queryDB($stmt, $params);

                return 'Log entry created successfully.';
            } else {
                return '<span class="bad">Log entries must have a title, category, and entry text.</span>';
            }
        } else {
            return 'This account can\'t create logs.';
        }
    }

    /**
     * Update log row in database
     *
     * @param json $logData JSON encoded log title, entry, and id
     *
     * @return string Confirmation message or error message
     */
    private function editLog($logData)
    {
        if ($_SESSION['rights']['editlog']) {
            $logData = (array) json_decode($logData);

            $editedlog = isset($logData['editlog']) ? $logData['editlog'] : '';
            $editedtitle = isset($logData['edittitle']) ? $logData['edittitle'] : '';
            $logid  = isset($logData['choosen']) ? $logData['choosen'] : '';

            if (!empty($editedlog) && !empty($editedtitle) && !empty($logid)) {
                $stmt = 'UPDATE `'.DB_PREFIX.'log` SET `title` = :eTitle, `entry` = :eEntry, `edited` = 1 WHERE `logid` = :logid';
                $params = array(
                    'eTitle' => urldecode($editedtitle),
                    'eEntry' => urldecode($editedlog),
                    'logid' => $logid
                );
                $this->queryDB($stmt, $params);

                return '"'.urldecode($editedtitle).'" edited successfully.';
            } else {
                return '<span class="bad">Log entries must have a title, category, and entry text.</span>';
            }
        } else {
            return 'This account can\'t edit logs';
        }
    }

    /**
     * Grab logs from database with given criteria
     *
     * @param json $filterQuery JSON encoded query parameters
     *      (type, keyword, date, category filter string)
     *
     * @return string HTML with filtered logs
     */
    private function filterLogs($filterQuery)
    {
        $notice = '';
        $query = (array) json_decode($filterQuery);
        $type = isset($query['type']) ? $query['type'] : '';

        // Category Search
        if ($type == '') {
            $filter = isset($query['filter']) ? urldecode($query['filter']) : '';
            $filter = rtrim($filter, ':');

            $notice .= <<<HTML
                <form>
                    <h3>**Filter applied: {$filter}**</h3>
                    <input type="button" value="Clear Filter" onClick="refreshLog('clearf')" />
                </form><br>
HTML;
            $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `cat` LIKE :filter ORDER BY `logid` DESC';
            $params = array(
                'filter' => "%".$filter."%"
            );

            $grab_logs = $this->queryDB($stmt, $params); // Sent to displaylogs function
        } else {
            $keyw = isset($query['keyw']) ? urldecode($query['keyw']) : '';
            $dates = isset($query['dates']) ? $query['dates'] : '';

            // Keyword search
            if ($type == "keyw") {
                $message = $keyw;

                $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `title` LIKE :keyw or `entry` LIKE :keyw ORDER BY `logid` DESC';
                $params = array(
                    'keyw' => "%".$keyw."%"
                );

                $grab_logs = $this->queryDB($stmt, $params); // Sent to displaylogs function
            }

            // Logs made on certain date
            else if ($type == "dates") {
                $message = $dates;

                $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `datec`=:dates ORDER BY `logid` DESC';
                $params = array(
                    'dates' => $dates
                );

                $grab_logs = $this->queryDB($stmt, $params); // Sent to displaylogs function
            }

            // Logs made on certain day containing keyword
            else {
                $message = $keyw.' on '.$dates;

                $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE (`title` LIKE :keyw or `entry` LIKE :keyw) and `datec`=:dates ORDER BY `logid` DESC';
                $params = array(
                    'keyw' => "%".$keyw."%",
                    'dates' => $dates
                );

                $grab_logs = $this->queryDB($stmt, $params); // Sent to displaylogs function
            }

            $notice .= <<<HTML
                <form>
                    <h3>Search results for: {$message}</h3>
                    <input type="button" value="Clear Search" onClick="refreshLog('clearf')" />
                </form><br>
HTML;
            
            $stmt = 'SELECT `userid`,`realname` FROM `'.DB_PREFIX.'users`';
            $userArray = $this->queryDB($stmt, NULL);
        }

        return $notice . DisplayLogs::display($grab_logs, true, $userArray);
    }

    /**
     * Grab logs from database
     *
     * @param json $data JSON encoded page offset or page number
     *
     * @return string Formatted log html
     */
    private function getLogs($data)
    {
        if ($_SESSION['rights']['viewlog']) {
            $pageInfo = (array) json_decode($data);

            // Initialize Variables
            $pageOffset = isset($pageInfo['pageOffset']) ? $pageInfo['pageOffset'] : '0';

            // For later use to specify a page number instead of offset ;)
            if (isset($pageInfo['page'])) {
                $pageOffset = ($pageInfo['page'] * $_SESSION['userInfo']['showlimit']) - $_SESSION['userInfo']['showlimit'];
            }

            $pageOffset = $pageOffset<0 ? '0' : $pageOffset; // If somehow the offset is < 0, make it 0

            // Grab row count of log table to determine offset
            $logSize = $this->numOfRows('log');

            // If the next page offset is > than the row count (which shouldn't happen
            // any more thanks to some logic in DisplayLogs class), make the offset the last
            // offset, (the current offset - the user page show limit).
            if ($pageOffset > $logSize) {
                $pageOffset = $pageOffset - $_SESSION['userInfo']['showlimit'];
            }

            // When using a SQL LIMIT, the parameter MUST be an integer.
            // To accomplish this the PDO constant PARAM_INT is passed
            $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` ORDER BY `logid` DESC LIMIT :pO,:lim';
            $params = array(
                'lim' => ((int) trim($_SESSION['userInfo']['showlimit'])),
                'pO' => ((int) trim($pageOffset))
            );

            $grab_logs = $this->queryDB($stmt, $params, \PDO::PARAM_INT);
            
            $stmt = 'SELECT `userid`,`realname` FROM `'.DB_PREFIX.'users`';
            $userArray = $this->queryDB($stmt, NULL);

            return DisplayLogs::display($grab_logs, false, $userArray, $pageOffset, $logSize);
        } else {
            return "This account does not have permission to view the activity log.";
        }
    }
}
