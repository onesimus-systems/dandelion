<?php
/**
  * @brief DisplayLogs shows the log entries provided
  *
  * This class is used whenever a script wants to display
  * a collection of log entries. display() is the only
  * public function.
  *
  * @author Lee Keitel
  * @date February 4, 2014
***/
class DisplayLogs {

    /** Called to display log entries given as $grab_logs
      *
      * @param grab_logs - Array of log entries that need to be displayed
      *
      * @return Nothing. All information is echoed
      */
    public static function display($grab_logs) {
        SELF::pageing(); // Show page controls
        
        SELF::showLogs($grab_logs); // Display log entries
        
        SELF::pageing(); // Show page controls
    }

    /** Displays pagintation controls
      *
      * @return Nothing. All information is echoed
      */
    private static function pageing() {
        global $isFiltered, $pageOffset, $logSize; /**< Grab needed variables **/
        
        // If this isn't filtered results, show the page controls
        if (!$isFiltered) {
            echo '<div class="pagination">';
            echo '<form method="post">';
            if ($pageOffset > 0) {
                echo '<input type="button" value="Previous '.$_SESSION['userInfo']['showlimit'].'" onClick="pagentation('. ($pageOffset-$_SESSION['userInfo']['showlimit']) .');" class="flle" />';
            }
            if ($pageOffset+$_SESSION['userInfo']['showlimit'] < $logSize[0]['COUNT(*)']) {
                echo '<input type="button" value="Next '.$_SESSION['userInfo']['showlimit'].'" onClick="pagentation('. ($pageOffset+$_SESSION['userInfo']['showlimit']) .');" class="flri" />';
            }
            echo '</form></div>';
        }
    }

    /** Displays log entries
      *
      * @param $grab_logs - Array supplied by display() contianing log entries to show
      *
      * @return Nothing. All information is echoed
      */
    private static function showLogs($grab_logs) {
    	global $isFiltered;
        // Grab a list of all current users and put them in an array
        $conn = new dbManage;
        $stmt = 'SELECT `userid`,`realname` FROM `'.DB_PREFIX.'users`';
        $userArray = $conn->queryDB($stmt, NULL);
		echo '<div id="refreshed_core">';
        
        foreach ($grab_logs as $row) {
        
            $creator = '';
            // Cycle through all users to find which one the entry belongs to
            foreach ($userArray as $user) {
                if ($row['usercreated'] == $user['userid']) {
                    $creator = $user['realname'];
                    break; // If the user is already found why go through the rest?
                }
            }
            
            if ($creator == '') { // If the creator doesn't exist, say something. Need to work on reattribution of deleted users
                $creator = 'Unknown User';
            }
            
            // Display each log entry
            echo '<form method="post">';
            echo '<div class="logentry">';
            echo '<h2>' . $row['title'] . '</h2>';
            echo '<p class="entry">' . nl2br($row['entry']) . '</p>';
            echo '<p class="entrymeta">Created by ' . $creator . ' on ' . $row['datec'] . ' @ ' . $row['timec'] . '. ';
            if ($row['edited']) { echo '(Edited)'; }
            echo '<br />Categorized as ' . $row['cat'] . '.';
            
            if (!$isFiltered) {
            	echo '<br /><a href="#" onClick="searchFun.filter(\'' . $row['cat'] . '\');">Learn more about this system...</a>';
            }
            
            if (($_SESSION['userInfo']['userid'] == $row['usercreated'] && $_SESSION['rights']['editlog']) OR $_SESSION['rights']['admin']) {
                ?>
                    <input type="button" value="Edit" onClick="editFun.grabedit(<?php echo $row['logid']; ?>);" class="flri" />
                <?php
            }
            
            echo '</p></div></form>';
        }
		echo '</div>';
    }
}