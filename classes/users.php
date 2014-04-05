<?php
/**
  * @brief User handles all user management tasks
  *
  * This class can be used to add, edit, and delete a user
  * 
  * @param conn - database connection object
  *
  * @author Lee Keitel
  * @date March, 2014
  * 
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
class User
{
	public $conn;
	
	public function __construct($conn) {
		$this->conn = $conn;
	}
	
	/** Update user information
	 *
	 * @param userInfo (keyed array) - User information in a associative array
	 * 		realname, sid, role, first, uid, theme
	 *
	 * @return Success message
	 */
	public function editUser($userInfoArray = null) {
		if (!empty($userInfoArray['realname']) &&
			!empty($userInfoArray['theme']) &&
			!empty($userInfoArray['role']) &&
			!empty($userInfoArray['first']) &&
			!empty($userInfoArray['uid']))
		{
			$stmt = 'UPDATE `'.DB_PREFIX.'users` SET `realname` = :realname, `role` = :role, `firsttime` = :first, `theme` = :theme WHERE `userid` = :userid';
			$params = array(
				'realname' => $userInfoArray['realname'],
				'role' => $userInfoArray['role'],
				'first' => $userInfoArray['first'],
				'userid' => $userInfoArray['uid'],
				'theme' => $userInfoArray['theme']
			);
	
			$this->conn->queryDB($stmt, $params);
	
			$stmt = 'UPDATE `'.DB_PREFIX.'presence` SET `realname` = :realname WHERE `uid` = :userid';
			$params = array(
				'realname' => $userInfoArray['realname'],
				'userid' => $userInfoArray['uid']
			);
	
			$this->conn->queryDB($stmt, $params);
	
			return 'User Updated<br><br>';
		}
		else {
			return 'Error 0x1c2u3e';
		}
	}

	/** Create a new user
	 *
	 * @param userInfo (keyed array) - User information in a associative array
	 * 		username, password, realname, sid, role
	 *
	 * @return Success message
	 */
	public function addUser($userInfoArray = null) {
		if (!empty($userInfoArray['username']) &&
			!empty($userInfoArray['password']) &&
			!empty($userInfoArray['realname']) &&
			!empty($userInfoArray['role']))
		{
			$stmt = 'SELECT * FROM `'.DB_PREFIX.'users` WHERE `username` = :username';
			$params = array(
				'username' => $userInfoArray['username']
			);
			$row = $this->conn->queryDB($stmt, $params);
	
			if ($row == NULL) {
				$date = new DateTime();
				$add_user = $userInfoArray['username'];
				$add_pass = password_hash($userInfoArray['password'], PASSWORD_BCRYPT);
				$add_real = $userInfoArray['realname'];
				$add_role = $userInfoArray['role'];
	
				$stmt = 'INSERT INTO `'.DB_PREFIX.'users` (username, password, realname, role, datecreated, theme) VALUES (:username, :password, :realname, :role, :datecreated, \'default\')';
				$params = array(
					'username' => $add_user,
					'password' => $add_pass,
					'realname' => $add_real,
					'role' => $add_role,
					'datecreated' => $date->format('Y-m-d')
				);    
				$this->conn->queryDB($stmt, $params);
	
				$lastID = $this->conn->lastInsertId();
	
				$stmt = 'INSERT INTO `'.DB_PREFIX.'presence` (`uid`, `realname`, `status`, `message`, `return`, `dmodified`) VALUES (:uid, :real, 1, \'\', \'00:00:00\', :date)';
				$params = array(
					'uid' => $lastID,
					'real' => $add_real,
					'date' => $date->format('Y-m-d H:i:s')
				);    
				$this->conn->queryDB($stmt, $params);
	
				return 'User Added<br><br>';
			}
			else {
				return 'Username already exists!';
			}
		}
		else {
			return 'Error 0x1c2u3a';
		}
	}

	/** Reset user password
	 *
	 * @param pass (string) - New password
	 * @param uid (int) - User's id number
	 *
	 * @return Success message
	 */
	public function resetUserPw($uid = null, $pass = null) {
		if (!empty($uid) && !empty($pass)) {
			if (is_numeric($uid)) {
				$pass = password_hash($pass, PASSWORD_BCRYPT);
		
				$stmt = 'UPDATE `'.DB_PREFIX.'users` SET `password` = :newpass WHERE `userid` = :myID';
				$params = array(
					'newpass' => $pass,
					'myID' => $uid
				);
				$this->conn->queryDB($stmt, $params);
		        
				return 'Password change successful.<br><br>';
			}
			else {
				return 'Error resetting password.<br><br>';
			}
		}
		else {
			return 'Error 0x1c2u3r';
		}
	}

	/** Delete user
	 *
	 * @param uid (int) - User's id number
	 *
	 * @return Success message
	 */
	public function deleteUser($uid = null) {
		if (!empty($uid)) {
			$stmt = 'DELETE FROM `'.DB_PREFIX.'users` WHERE `userid` = :userid';
			$stmt2 = 'DELETE FROM `'.DB_PREFIX.'presence` WHERE `uid` = :userid';
			$params = array(
				'userid' => $uid
			);
		
			$this->conn->queryDB($stmt, $params);
			$this->conn->queryDB($stmt2, $params);
		
			return "Action Taken: User Deleted<br><br>";
		}
		else {
			return 'Error 0x1c2u3d';
		}
	}
	
	/** Update user status
	 *
	 * @param uid (int) - User's id number
	 * @param status_id (int) - # for user status type
	 * @param message (string) - User's away message
	 * @param returntime (date/time) - Return time for away user
	 *
	 * @return Success message
	 */
	public function updateUserStatus($uid = null, $status_id = null, $message = null, $returntime = null) {
		if (!empty($uid) &&
			!empty($status_id))
		{
			$date = new DateTime();
			$date = $date->format('Y-m-d H:i:s');
	
			switch($status_id) {
				case "Available":
					$status_id = 1;
					$returntime = '00:00:00';
					$message = '';
					break;
				case "Away From Desk":
					$status_id = 2;
					break;
				case "At Lunch":
					$status_id = 3;
					break;
				case "Out for Day":
					$status_id = 4;
					break;
				case "Out":
					$status_id = 5;
					break;
				case "Appointment":
					$status_id = 6;
					break;
				case "Do Not Disturb":
					$status_id = 7;
					break;
				case "Meeting":
					$status_id = 8;
					break;
				case "Out Sick":
					$status_id = 9;
					break;
				case "Vacation":
					$status_id = 10;
					break;
				default:
					$status_id = 1;
					$returntime = "00:00:00";
					break;
			}
	
			$stmt = 'UPDATE `'.DB_PREFIX.'presence` SET `message` = :message, `status` = :status, `return` = :return, `dmodified` = :date WHERE `uid` = :userid';
			$params = array(
				'message' => $message,
				'status' => $status_id,
				'return' => $returntime,
				'date' => $date,
				'userid' => $uid
			);
			$this->conn->queryDB($stmt, $params);
			
			return 'User Status Updated<br><br>';
		}
		else {
			return 'Error 0x1c2u3c';
		}
	}
}