<?php
/**
 * Category management
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\CategoriesRepo;

class Categories
{
    private $repo;

    public function __construct(CategoriesRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get the children of a parent category and generate a <select>
     * element with the root node and all children
     *
     * @param array $past History of category nodes in parentid:level notation
     *
     * @return string - HTML of category select group
     */
    public function renderChildren($past)
    {
        $cat = $this->repo->getAllCategories();

        $response = '';

        foreach ($past as $key => $value) {
            $past[$key] = explode(':', $value);
        }

        $i = 0;
        foreach ($past as $pastSel) {
            $newSel = '<select name="level'.($pastSel[1]+1).'" id="level'.($pastSel[1]+1).'" onChange="CategoryManage.grabNextLevel(this);">';
            $newSel .= '<option value="Select:">Select:</option>';
            $option = '';

            $alphaList = array();
            foreach ($cat as $isChild) {
                if($isChild['pid'] == $pastSel[0]) {
                    $child = array(
                            'cid' =>  $isChild['cid'],
                            'description' => $isChild['description']
                    );
                    array_push($alphaList, $child);
                }
            }

            usort($alphaList, "self::cmp");

            foreach ($alphaList as $children) {
                    $selected = (isset($past[$i+1][0]) && ($children['cid'] == $past[$i+1][0])) ? 'selected' : '';
                    $option = '<option value="'.$children['cid'].':'.($pastSel[1]+1).'"'.$selected.'>'.$children['description'].'</option>';
                    $newSel .= $option;
            }

            $newSel .= '</select>';

            if (!empty($option)) {
                // If there are sub categories, echo the selectbox
                $response .= $newSel;
            }
            $i++;
        }

        return $response;
    }

    public function renderFromString($catstring)
    {
        $catstring = explode(':', $catstring);
        $idArr = ['0:0'];
        $parent = 0;

        for ($i = 0; $i < count($catstring); $i++) {
            $pid = $this->repo->getIdForCategoryWithParent($catstring[$i], $parent);
            $parent = $pid;
            array_push($idArr, $pid.':'.($i+1));
        }

        return $this->renderChildren($idArr);
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
     * @return string - Status message
     */
    public function addCategory($parent, $description)
    {
        $description = str_replace(':', '_', $description);

        if ($this->repo->addCategory($description, $parent)) {
            return 'Category added successfully';
        } else {
            return 'Error adding category';
        }
    }

    /**
     * Remove category from database
     *
     * @param int $cid ID of category to be deleted
     *
     * @return string - Status message
     */
    public function delCategory($cid)
    {
        // Get the category's current parent to reassign children
        $newParent = $this->repo->getCategoryParent($cid);

        // Delete category from DB
        $this->repo->deleteCategory($cid);

        // Reassign children
        $this->repo->adoptChildren($newParent, $cid);

        return 'Category deleted successfully';
    }

    /**
     * Update category description
     *
     * @param int $cid ID of category to update
     * @param string $desc Name of category
     *
     * @return string - Status message
     */
    public function editCategory($cid, $desc)
    {
        $desc = str_replace(':', '_', $desc);

        if ($this->repo->updateCategory($desc, $cid)) {
            return 'Category updated successfully';
        } else {
            return 'Error saving category';
        }
    }
}
