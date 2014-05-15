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
}