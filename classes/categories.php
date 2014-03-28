<?php
/**
 * @brief Categories is responsible for displaying and managing categories
 *
 * This class displays all categories and manages the addition, deletion,
 * and manipulation of the same.
 *
 * @author Lee Keitel
 * @date February 11, 2014
 ***/
class Categories
{
	private $conn;
	
	function __construct($init = true) {
		if ($init) {
			$this->conn = new dbManage();
		}
	}
	
	public function getChildren($parentID, $past) {
		$cat = $this->conn->selectFrom(NULL, 'category');
		
		$parent = explode(':', $parentID);
		$response = '';
		
		foreach($past as $pastSel) {
			$pastSel = explode(':', $pastSel);
			
			$newSel = '<select name="level'.($pastSel[1]+1).'" id="level'.($pastSel[1]+1).'" onChange="CategoryManage.grabNextLevel(this);">';
			$newSel .= '<option value="Select:">Select:</option>';
			$option = '';
			
			$alphaList = array();
			foreach($cat as $isChild)
			{
				if($isChild['pid'] == $pastSel[0])
				{
					$child = array(
							'cid' =>  $isChild['cid'],
							'desc' => $isChild['desc']
					);
					array_push($alphaList, $child);
				}
			}
			
			usort($alphaList, "self::cmp");
			
			foreach($alphaList as $children) {
					$option = '<option value="'.$children['cid'].':'.($pastSel[1]+1).'">'.$children['desc'].'</option>';
					$newSel .= $option;
			}
			
			$newSel .= '</select>';
		
			if (!empty($option)) {
				// If there are sub categories, echo the selectbox
				$response .= $newSel;
			}
		}
		
		echo $response;
	}
	
	private function cmp($a, $b) {
		return strcmp($a['desc'], $b['desc']);
	}
	
	public function addCategory($parent, $description) {
		$stmt = 'INSERT INTO `category` (`desc`, `pid`) VALUES (:description, :parentid)';
		$params = array(
			'description' => $description,
			'parentid'	  => $parent
		);
		
		if ($this->conn->queryDB($stmt, $params)) {
			echo '';
		} else {
			echo 'Error adding category';
		}
	}
	
	public function delCategory($cid) {
		// Get the category's current parent to reassign children
		$params = array(
			'catid' => $cid
		);
		
		$newParent = $this->conn->selectFrom('pid', 'category', '`cid` = :catid', $params);
		$newParent = $newParent[0]['pid'];
		
		// Delete category from DB
		$params = array(
			'catid' => $cid
		);
		$this->conn->deleteFrom('category', '`cid` = :catid', $params);
		
		// Reassign children
		$stmt = 'UPDATE `category` SET `pid` = :newp WHERE `pid` = :oldp';
		$params = array(
			'newp' => $newParent,
			'oldp' => $cid
		);
		$this->conn->queryDB($stmt, $params);
				
		echo 'Category deleted successfully';
	}
	
	public function editCategory($cid, $desc) {
		$stmt = 'UPDATE `category` SET `desc` = :desc WHERE `cid` = :cid';
		$params = array(
				'desc' => $desc,
				'cid' => $cid
		);
		
		if ($this->conn->queryDB($stmt, $params)) {
			echo 'Category updated successfully';
		}
		else {
			echo 'Error saving category';
		}
	}
}
