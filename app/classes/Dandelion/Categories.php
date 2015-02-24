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
     * Get the children of a parent category and return JSON of list at each level
     *
     * @param array $cids - Category IDs of selected starting with 0 at index 0 as root
     *
     * @return string - HTML of category select group
     */
    public function renderChildrenJson($cids)
    {
        $cats = $this->repo->getAllCategories();

        $response = [
            'currentList' => $cids,
            'levels' => []
        ];

        // Cycle through each level
        foreach ($cids as $i => $cid) {
            $alphaList = array();
            // Find children
            foreach ($cats as $isChild) {
                if($isChild['pid'] == $cid) {
                    $child = array(
                        'cid' =>  $isChild['cid'],
                        'description' => $isChild['description']
                    );
                    array_push($alphaList, $child);
                }
            }

            // Sort children alphabetically
            usort($alphaList, "self::cmp");

            // Add children to array for the level
            foreach ($alphaList as $children) {
                $selected = (isset($cids[$i+1]) && ($children['cid'] == $cids[$i+1])) ? true : false;

                $response['levels'][$i][] = [
                    'id' => $children['cid'],
                    'desc' => $children['description'],
                    'selected' => $selected
                ];
            }
        }

        return json_encode($response);
    }

    public function renderFromString($catstring)
    {
        $catstring = explode(':', $catstring);
        $idArr = [0];
        $pid = 0;

        for ($i = 0; $i < count($catstring); $i++) {
            $pid = $this->repo->getIdForCategoryWithParent($catstring[$i], $pid);
            array_push($idArr, $pid);
        }
        return $this->renderChildrenJson($idArr);
    }

    /**
     * Used with usort() to alphabetize the category lists
     */
    private function cmp($a, $b)
    {
        return strcasecmp($a['description'], $b['description']);
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
        $deleted = $this->repo->deleteCategory($cid);

        // Reassign children
        $reassigned = $this->repo->adoptChildren($newParent, $cid);

        if ($deleted && $reassigned) {
            return 'Category deleted successfully';
        } else {
            return 'An error occured deleting a category';
        }
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
