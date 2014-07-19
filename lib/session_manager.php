<?php
/**
 * Functions to manage PHP sessions manually with database
 *
 * @author Lee Keitel
 * @date July, 2014
 *        
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 *         
 */
$sdbc = null;

function open_session() {
    global $sdbc;
    
    $sdbc = new \Dandelion\Database\dbManage();
    
    return true;
}

function close_session() {
    global $sdbc;
    
    unset($sdbc);
}

function read_session($sid) {
    global $sdbc;
    
    $sql = "SELECT data
            FROM " . DB_PREFIX . "sessions
            WHERE id=:sid";
    
    $params = array(
        'sid' => $sid 
    );
    
    $r = $sdbc->queryDB($sql, $params);
    
    if (count($r) == 1) {
        $data = $r[0]["data"];
        return $data;
    }
    else {
        return '';
    }
}

function write_session($sid, $data) {
    global $sdbc;
    
    $sql = "INSERT
            INTO " . DB_PREFIX . "sessions (id, data)
            VALUES (:id, :data)
            ON DUPLICATE KEY
                UPDATE
                id = :id,
                data = :data";
    
    $params = array(
        'id' => $sid,
        'data' => $data 
    );
    
    $sdbc->queryDB($sql, $params);
    
    return true;
}

function destroy_session($sid) {
    global $sdbc;
    
    $sql = "DELETE
            FROM " . DB_PREFIX . "sessions
            WHERE id=:id";
    
    $params = array(
        'id' => $sid 
    );
    
    $sdbc->queryDB($sql, $params);
    
    $_SESSION = array();
    
    return true;
}

function clean_session($expire) {
    global $sdbc;
    
    $sql = "DELETE
            FROM " . DB_PREFIX . "sessions
            WHERE DATA_ADD(last_accessed, INTERVAL :expired SECOND) < NOW()";
    
    $params = array(
        'expired' => $expire 
    );
    
    $sdbc->queryDB($sql, $params);
    
    return true;
}

session_set_save_handler('open_session', 'close_session', 'read_session', 'write_session', 'destroy_session', 'clean_session');