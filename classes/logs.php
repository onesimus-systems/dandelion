<?php
/**
  * Handles all requests pertaining to log entries
  *
  * @author Lee Keitel
  * @date May 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

class Logs
{
    public static function doAction($data) {
        $conn = new dbManage();
        return self::$data['action']($conn, $data['data']);
    }
    
    public static function getLogInfo($conn, $logid) {
    	$loguid = isset($loguid) ? $loguid : '';
    
    	$stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `logid` = :logid';
    	$params = array(
    	    'logid' => $logid
    	);
    	
    	$edit_log_info = $conn->queryDB($stmt, $params);
    
    	return json_encode($edit_log_info[0]);
    }
    
    public static function addLog($conn, $logData) {
        if ($_SESSION['rights']['createlog']) {
            $logData = (array) json_decode($logData);
            
            $new_title = isset($logData['add_title']) ? $logData['add_title'] : '';
            $new_entry = isset($logData['add_entry']) ? $logData['add_entry'] : '';
            $new_category = isset($logData['cat']) ? $logData['cat'] : NULL;
        
            // Check that all required fields have been entered
            if ($new_title != NULL AND $new_title != '' AND
                $new_entry != NULL AND $new_entry != '' AND
                $new_category != NULL AND $new_category != 'Select:' AND $new_category != 'false')
            {
                $datetime = getdate();
                $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
                $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];
                
                // Add new entry
                $stmt = 'INSERT INTO `'.DB_PREFIX.'log` (datec, timec, title, entry, usercreated, cat)  VALUES (:datec, :timec, :title, :entry, :usercreated, :cat)';
                $params = array(
                    'datec' => $new_date,
                    'timec' => $new_time,
                    'title' => urldecode($new_title),
                    'entry' => urldecode($new_entry),
                    'usercreated' => $_SESSION['userInfo']['userid'],
                    'cat' => urldecode($new_category),
                );
                $conn->queryDB($stmt, $params);
                
                return "Log entry created successfully.";
            }
            else {
                return '<span class="bad">Log entries must have a title, category, and entry text.</span>';
            }
        }
        
        else {
            return 'This account can\'t create logs.';
        }
    }
    
    public static function editLog($conn, $logData) {
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
            	$conn->queryDB($stmt, $params);
            	
            	return '"'.urldecode($editedtitle).'" edited successfully.';
            }
            else {
                return '<span class="bad">Log entries must have a title, category, and entry text.</span>';
            }
        }
        
        else {
            return 'This account can\'t edit logs';
        }
    }
    
    public static function filterLogs($conn, $filterQuery) {
        $query = (array) json_decode($filterQuery);
    	$type = isset($query['type']) ? $query['type'] : '';
    	
    	// Category Search
    	if ($type == '') {
            $filter = isset($query['filter']) ? urldecode($query['filter']) : '';
            $filter = rtrim($filter, ':');
    	    ?>
    		    <form>
    		        <h3>**Filter applied: <?php echo $filter; ?>**</h3>
    		        <input type="button" value="Clear Filter" onClick="refreshLog('clearf')" />
    		    </form>
    	    <?php
    	    $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `cat` LIKE :filter ORDER BY `logid` DESC';
    	    $params = array(
    	        'filter' => "%".$filter."%"
    	    );
    	    $grab_logs = $conn->queryDB($stmt, $params);
    	}
    	
    	else {
        	$keyw = isset($query['keyw']) ? urldecode($query['keyw']) : '';
        	$dates = isset($query['dates']) ? $query['dates'] : '';
        	
    	    // Keyword search
    	    if ($type == "keyw") {
    	        $message = $keyw;
    	        
    	        $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `title` LIKE :keyw or `entry` LIKE :keyw ORDER BY `logid` DESC';
    	        $params = array(
    	            'keyw' => "%".$keyw."%"
    	        );
    	        $grab_logs = $conn->queryDB($stmt, $params);
    	    }
    	    // Logs made on certain date
    	    else if ($type == "dates") {
    	        $message = $dates;
    	        
    	        $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE `datec`=:dates ORDER BY `logid` DESC';
    	        $params = array(
    	            'dates' => $dates
    	        );
    	        $grab_logs = $conn->queryDB($stmt, $params);
    	    }
    	    // Logs made on certain day containing keyword
    	    else {
    	        $message = $keyw.' on '.$dates;
    	
    	        $stmt = 'SELECT * FROM `'.DB_PREFIX.'log` WHERE (`title` LIKE :keyw or `entry` LIKE :keyw) and `datec`=:dates ORDER BY `logid` DESC';
    	        $params = array(
    	            'keyw' => "%".$keyw."%",
    	            'dates' => $dates
    	        );
    	        $grab_logs = $conn->queryDB($stmt, $params);
    	    }
    	    ?>
    		    <form>
    		        <h3 style="display:inline;">Search results for: <?php echo $message; ?></h3>
    		        <input type="button" value="Clear Search" onClick="refreshLog('clearf')" />
    		    </form>
    	    <?php
    	}
    	
    	$isFiltered = true; // Don't show paging controls
    	
    	// Display filtered logs
    	DisplayLogs::display($grab_logs);
    }
}