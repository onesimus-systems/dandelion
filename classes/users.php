<?php
/**
  * @brief User handles all user management tasks
  *
  * This class can be used to add, edit, and delete a user
  *
  * @author Lee Keitel
  * @date March, 2014
***/
class User
{        
	public function editUser($userInfoArray) {
		if (!empty($userInfoArray)) {
			$stmt = 'UPDATE `users` SET `realname` = :realname, `settings_id` = :s_id, `role` = :role, `firsttime` = :first, `theme` = :theme WHERE `userid` = :userid';
			$params = array(
				'realname' => $userInfoArray['realname'],
				's_id' => $userInfoArray['sid'],
				'role' => $userInfoArray['role'],
				'first' => $userInfoArray['first'],
				'userid' => $userInfoArray['uid'],
				'theme' => $userInfoArray['theme']
			);

			$conn->queryDB($stmt, $params);

			$stmt = 'UPDATE `presence` SET `realname` = :realname WHERE `uid` = :userid';
			$params = array(
				'realname' => $userInfoArray['realname'],
				'userid' => $userInfoArray['uid']
			);

			$conn->queryDB($stmt, $params);

			return 'User Updated<br /><br />';
		}
		else {
			return false;
		}
	}
        
	public function addUser($userInfoArray) {
		if (!empty($userInfoArray)) {
			$stmt = 'SELECT * FROM `users` WHERE `username` = :username';
			$params = array(
				'username' => $userInfoArray['username']
			);
			$row = $conn->queryDB($stmt, $params);

			if ($row == NULL) {
				$date = new DateTime();
				$add_user = $userInfoArray['username'];
				$add_pass = password_hash($userInfoArray['password'], PASSWORD_BCRYPT);
				$add_real = $userInfoArray['realname'];
				$add_sid = $userInfoArray['sid'];
				$add_role = $userInfoArray['role'];

				$stmt = 'INSERT INTO users (username, password, realname, settings_id, role, datecreated, theme) VALUES (:username, :password, :realname, :s_id, :role, :datecreated, \'default\')';
				$params = array(
					'username' => $add_user,
					'password' => $add_pass,
					'realname' => $add_real,
					's_id' => $add_sid,
					'role' => $add_role,
					'datecreated' => $date->format('Y-m-d')
				);    
				$conn->queryDB($stmt, $params);

				$lastID = $conn->lastInsertId();

				$stmt = 'INSERT INTO `presence` (`uid`, `realname`, `status`, `message`, `return`, `dmodified`) VALUES (:uid, :real, 1, \'\', \'00:00:00\', :date)';
				$params = array(
					'uid' => $lastID,
					'real' => $add_real,
					'date' => $date->format('Y-m-d H:i:s')
				);    
				$conn->queryDB($stmt, $params);

				return 'User Added<br /><br />';
			}

			else {
				return 'Username already exists!';
			}
		}
		else {
			return false;
		}
	}
        
	public function resetUserPw($pass1, $pass2, $uid) {
		if ($pass1 == $pass2 && $pass1 != '') {
			$pass1 = password_hash($pass1, PASSWORD_BCRYPT);

			$stmt = 'UPDATE `users` SET `password` = :newpass WHERE `userid` = :myID';
			$params = array(
				'newpass' => $pass1,
				'myID' => $uid
			);
			$conn->queryDB($stmt, $params);
	        
			return 'Password change successful.<br /><br />';
		}
		else {
			return false;
		}
	}
        
	public function deleteUser($uid) {
		$stmt = 'DELETE FROM `users` WHERE `userid` = :userid';
		$stmt2 = 'DELETE FROM `presence` WHERE `uid` = :userid';
		$params = array(
			'userid' => $choosen
		);
	
		$conn->queryDB($stmt, $params);
		$conn->queryDB($stmt2, $params);
	
		echo "Action Taken: User Deleted<br /><br />";
	}
        
	public function updateUserStatus($uid, $status_id, $message, $returntime) {
		$date = new DateTime();
		$date = $date->format('Y-m-d H:i:s');

		switch($status_id) {
			case "Available":
				$status_id = 1;
				$returntime = "00:00:00";
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

		$stmt = 'UPDATE `presence` SET `message` = :message, `status` = :status, `return` = :return, `dmodified` = :date WHERE `uid` = :userid';
		$params = array(
			'message' => $message,
			'status' => $status_id,
			'return' => $returntime,
			'date' => $date,
			'userid' => $uid
		);
		$conn->queryDB($stmt, $params);
		
		return 'User Status Updated<br /><br />';
	}
}