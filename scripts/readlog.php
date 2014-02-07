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
    public function display($grab_logs) {
        // TODO: Create a blacklist feature
        if ($_SESSION['userInfo']['username'] != "ajmartin") {
 
            $this->pageing(); // Show page controls
            
            $this->showLogs($grab_logs); // Display log entries
            
            $this->pageing(); // Show page controls
        }
        else {
            echo "This account does not have permission to view the activity log."; // Angie
        }
    }

    /** Displays pagintation controls
      *
      * @return Nothing. All information is echoed
      */
    private function pageing() {
        global $isFiltered, $pageOffset, $logSize; /**< Grab needed variables **/
        
        // If this isn't filtered results, show the page controls
        if (!$isFiltered) {
            echo '<div style="display: block;overflow:hidden;">';
            echo '<form method="post">';
            if ($pageOffset > 0) {
                echo '<input type="button" value="<- Previous" onClick="pagentation('. ($pageOffset-$_SESSION['userInfo']['showlimit']) .');" class="flle" />';
            }
            if ($pageOffset+$_SESSION['userInfo']['showlimit'] < $logSize[0]['COUNT(*)']) {
                echo '<input type="button" value="Next ->" onClick="pagentation('. ($pageOffset+$_SESSION['userInfo']['showlimit']) .');" class="flri" />';
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
    private function showLogs($grab_logs) {
        // Grab a list of all current users and put them in an array
        $conn = new dbManage;
        $stmt = 'SELECT `userid`,`realname` FROM users';
        $userArray = $conn->queryDB($stmt, NULL);
        
        foreach ($grab_logs as $row) {
        
            // Cycle through all users to find which one the entry belongs to
            foreach ($userArray as $user) {
                if ($row['usercreated'] == $user['userid']) {
                    $creator = $user['realname'];
                    break; // If the user is already found why go through the rest?
                }
            }
            
            // Display each log entry
            echo '<form method="post">';
            echo '<div class="logentry">';
            echo '<h2>' . $row['title'] . '</h2>';
            echo '<p class="entry">' . nl2br($row['entry']) . '</p>';
            echo '<p class="entrymeta">Created by ' . $creator . ' on ' . $row['datec'] . ' @ ' . $row['timec'] . '. ';
            if ($row['edited']) { echo '(Edited)'; }
            echo '<br />Categorized as ' . $row['cat'] . '.';
            
            if ($_SESSION['userInfo']['userid'] == $row['usercreated'] OR ($_SESSION['userInfo']['userid'] == $row['usercreated'] AND $_SESSION['userInfo']['role'] == 'admin')) {
                ?>
                    <input type="button" value="Edit" onClick="editFun.grabedit(<?php echo $row['logid']; ?>);" class="flri" />
                <?php
            }
            elseif ($_SESSION['userInfo']['role'] == 'admin') {
                ?>
                    <input type="button" value="Edit as Admin" onClick="editFun.grabedit(<?php echo $row['logid']; ?>);" class="flri" />
                <?php
            }
            
            echo '</p></div></form>';
        }
    }
}