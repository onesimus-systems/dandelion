<?php
/**
 * @brief Permissions is responsible for all permissions handling in Dandelion.
 *
 * @author Lee Keitel
 * @date May 2014
 * 
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
class Permissions
{
	private $conn;
	
	function __construct($init = true) {
		if ($init) {
			$this->conn = new dbManage();
		}
	}
	
	function getGroupList($groupID = NULL) {
		if ($groupID === NULL) {
			$stmt = 'SELECT * FROM '.DB_PREFIX.'rights';
			$params = NULL;
		}
		else {
			$stmt = 'SELECT * FROM '.DB_PREFIX.'rights WHERE id = :id';
			$params = array(
				'id' => $groupID
			);
		}
		
		return $this->conn->queryDB($stmt, $params);
	}
	
	function createGroup() {
		
	}
	
	function deleteGroup() {
		
	}
	
	function editGroup($gid, $rights) {
	    $rights = serialize($rights);
	    
		$stmt = 'UPDATE `'.DB_PREFIX.'rights` SET `permissions` = :newPerm WHERE `id` = :gid';
		$params = array(
		    'gid' => $gid,
		    'newPerm' => $rights
		);
		
		return $this->conn->queryDB($stmt, $params);
	}
	
	function checkRights() {
		
	}
}
