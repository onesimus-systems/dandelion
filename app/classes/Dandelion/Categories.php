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
                if($isChild['parent'] == $cid) {
                    $child = array(
                        'id' =>  $isChild['id'],
                        'description' => $isChild['description']
                    );
                    array_push($alphaList, $child);
                }
            }

            // Sort children alphabetically
            usort($alphaList, "self::cmp");

            // Add children to array for the level
            foreach ($alphaList as $children) {
                $selected = (isset($cids[$i+1]) && ($children['id'] == $cids[$i+1])) ? true : false;

                $response['levels'][$i][] = [
                    'id' => $children['id'],
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
            if ($pid) {
                array_push($idArr, $pid);
            }
        }

        $mainJson = json_decode($this->renderChildrenJson($idArr), true);
        if ((count($catstring) + 1) > count($idArr)) {
            $mainJson['error'] = true;
        } else {
            $mainJson['error'] = false;
        }
        return json_encode($mainJson);
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
     * @return bool - success
     */
    public function addCategory($parent, $description)
    {
        $description = str_replace(':', '_', $description);

        return $this->repo->addCategory($description, $parent);
    }

    /**
     * Remove category from database
     *
     * @param int $cid ID of category to be deleted
     *
     * @return bool - success
     */
    public function delCategory($cid)
    {
        // Get the category's current parent to reassign children
        $newParent = $this->repo->getCategoryParent($cid);

        // Delete category from DB
        $deleted = $this->repo->deleteCategory($cid);

        // Reassign children
        $this->repo->adoptChildren($newParent, $cid);

        return $deleted
    }

    /**
     * Update category description
     *
     * @param int $cid ID of category to update
     * @param string $desc Name of category
     *
     * @return bool - success
     */
    public function editCategory($cid, $desc)
    {
        $desc = str_replace(':', '_', $desc);

        return $this->repo->updateCategory($desc, $cid);
    }
}
