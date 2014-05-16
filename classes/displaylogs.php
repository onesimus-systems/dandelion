<?php
/**
  * Show the logs supplied through the display() method.
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
  * @date Feb 2014
***/
namespace Dandelion;
use Dandelion\Database\dbManage;

/**
 * This class is used whenever a script wants to display
 * a collection of log entries. display() is the only
 * public function.
 */
class displaylogs
{
    /**
     * Called to display log entries
     *
     * @param array $grab_logs Log entries that need to be displayed
     * @param bool $filtered Is this request for filtered logs or not
     * @param int $pageOffset The lower limit used to determine page breaks
     * @param int $logSize Number of total rows of log data
     *
     * @return string HTML of log data and pagination controls
     */
    public static function display($grab_logs, $filtered, $pageOffset, $logSize)
    {
        $logHtml = '';

        if (!$filtered)
            $logHtml .= SELF::pageing($pageOffset, $logSize); // Show page controls

        $logHtml .= SELF::showLogs($grab_logs, $filtered); // Display log entries

        if (!$filtered)
            $logHtml .= SELF::pageing($pageOffset, $logSize); // Show page controls

        return $logHtml;
    }

    /**
     * Displays pagination controls
     *
     * @param int $pageOffset The lower limit used to determine page breaks
     * @param int $logSize Number of total rows of log data
     *
     * @return string HTML pagination controls
     */
    private static function pageing($pageOffset, $logSize)
    {
        $pageControls = '';

        $pageControls .= '<div class="pagination">';
        $pageControls .= '<form method="post">';
        if ($pageOffset > 0) {
            $pageControls .= '<input type="button" value="Previous '.$_SESSION['userInfo']['showlimit'].'" onClick="pagentation('. ($pageOffset-$_SESSION['userInfo']['showlimit']) .');" class="flle" />';
        }
        if ($pageOffset+$_SESSION['userInfo']['showlimit'] < $logSize) {
            $pageControls .= '<input type="button" value="Next '.$_SESSION['userInfo']['showlimit'].'" onClick="pagentation('. ($pageOffset+$_SESSION['userInfo']['showlimit']) .');" class="flri" />';
        }
        $pageControls .= '</form></div>';

        return $pageControls;
    }

    /**
     * Displays log entries
     *
     * @param array $grab_logs Log entries that need to be displayed
     * @param bool $isFiltered Is this request for filtered logs or not
     *
     * @return string HTML of log data
     *
     * @TODO Improve attribution of deleted users
     */
    private static function showLogs($grab_logs, $isFiltered)
    {
        $logList = '';

        // Grab a list of all current users and put them in an array
        $conn = new dbManage;
        $stmt = 'SELECT `userid`,`realname` FROM `'.DB_PREFIX.'users`';
        $userArray = $conn->queryDB($stmt, NULL);

        $logList .= '<div id="refreshed_core">';

        foreach ($grab_logs as $row) {
            $creator = '';
            // Cycle through all users to find which one the entry belongs to
            foreach ($userArray as $user) {
                if ($row['usercreated'] == $user['userid']) {
                    $creator = $user['realname'];
                    break;
                }
            }

            if ($creator == '') {
                $creator = 'Unknown User';
            }

            // Display each log entry
            $logList .= '<form method="post">';
            $logList .= '<div class="logentry">';
            $logList .= '<h2>' . $row['title'] . '</h2>';
            $logList .= '<p class="entry">' . nl2br($row['entry']) . '</p>';
            $logList .= '<p class="entrymeta">Created by ' . $creator . ' on ' . $row['datec'] . ' @ ' . $row['timec'] . '. ';
            if ($row['edited']) { $logList .= '(Edited)'; }
            $logList .= '<br />Categorized as ' . $row['cat'] . '.';

            if (!$isFiltered) {
                $logList .= '<br /><a href="#" onClick="searchFun.filter(\'' . $row['cat'] . '\');">Learn more about this system...</a>';
            }

            if (($_SESSION['userInfo']['userid'] == $row['usercreated'] && $_SESSION['rights']['editlog']) OR $_SESSION['rights']['admin']) {
                $logList .= "<input type=\"button\" value=\"Edit\" onClick=\"editFun.grabedit({$row['logid']});\" class=\"flri\" />";
            }

            $logList .= '</p></div></form>';
        }
        $logList .= '</div>';

        return $logList;
    }
}
