<?php
/**
  * Responsible for displaying and managing categories
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

/**
 * This class displays all categories and manages the addition, deletion,
 * and manipulation of the same.
 */
class categories extends Database\dbManage
{
    /**
     * Get the children of a parent category and generate a <select>
     * element with the root node and all children
     *
     * @param array $past History of category nodes in parentid:level notation
     *
     * @return echo
     */
    public function getChildren($past)
    {
        $cat = $this->selectAll('category');

        $response = '';

        foreach($past as $pastSel) {
            $pastSel = explode(':', $pastSel);

            $newSel = '<select name="level'.($pastSel[1]+1).'" id="level'.($pastSel[1]+1).'" onChange="CategoryManage.grabNextLevel(this);">';
            $newSel .= '<option value="Select:">Select:</option>';
            $option = '';

            $alphaList = array();
            foreach($cat as $isChild) {
                if($isChild['pid'] == $pastSel[0]) {
                    $child = array(
                            'cid' =>  $isChild['cid'],
                            'description' => $isChild['description']
                    );
                    array_push($alphaList, $child);
                }
            }

            usort($alphaList, "self::cmp");

            foreach($alphaList as $children) {
                    $option = '<option value="'.$children['cid'].':'.($pastSel[1]+1).'">'.$children['description'].'</option>';
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

    /**
     * Used with usort() to alphabetize the category lists
     */
    private function cmp($a, $b)
    {
        return strcmp($a['description'], $b['description']);
    }

    /**
     * Create a new category
     *
     * @param int $parent Parent ID (0 if root)
     * @param string $description Name of category
     *
     * @return echo
     */
    public function addCategory($parent, $description)
    {
        $stmt = 'INSERT INTO `'.DB_PREFIX.'category` (`description`, `pid`) VALUES (:description, :parentid)';
        $params = array(
            'description' => $description,
            'parentid'	  => $parent
        );

        if ($this->queryDB($stmt, $params)) {
            echo 'Category added successfully';
        } else {
            echo 'Error adding category';
        }
    }

    /**
     * Remove category for database
     *
     * @param int $cid ID of category to be deleted
     *
     * @return echo
     */
    public function delCategory($cid)
    {
        // Get the category's current parent to reassign children
        $stmt = 'SELECT `pid` FROM `'.DB_PREFIX.'category` WHERE `cid` = :catid';
        $params = array(
            'catid' => $cid
        );

        $newParent = $this->queryDB($stmt, $params);
        $newParent = $newParent[0]['pid'];

        // Delete category from DB
        $stmt = 'DELETE FROM `'.DB_PREFIX.'category` WHERE `cid` = :catid';
        $params = array(
            'catid' => $cid
        );
        $this->queryDB($stmt, $params);

        // Reassign children
        $stmt = 'UPDATE `'.DB_PREFIX.'category` SET `pid` = :newp WHERE `pid` = :oldp';
        $params = array(
            'newp' => $newParent,
            'oldp' => $cid
        );
        $this->queryDB($stmt, $params);

        echo 'Category deleted successfully';
    }

    /**
     * Update category description
     *
     * @param int $cid ID of category to update
     * @param string $desc Name of category
     *
     * @return echo
     */
    public function editCategory($cid, $desc)
    {
        $stmt = 'UPDATE `'.DB_PREFIX.'category` SET `description` = :desc WHERE `cid` = :cid';
        $params = array(
                'desc' => $desc,
                'cid' => $cid
        );

        if ($this->queryDB($stmt, $params)) {
            echo 'Category updated successfully';
        } else {
            echo 'Error saving category';
        }
    }
}
