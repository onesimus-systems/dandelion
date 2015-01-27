<?php
/**
 *  Category management API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Categories;
use \Dandelion\Controllers\ApiController;

class categoriesAPI extends BaseModule
{
    public function __construct($db, $ur, $params)
    {
        parent::__construct($db, $ur, $params);
    }

    /**
     *  Add new category
     *
     *  @return JSON
     */
    public function add()
    {
        if (!$this->ur->authorized('addcat')) {
            exit(ApiController::makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $parent = $this->up->parentID;
        $desc = $this->up->catDesc;
        $createCat = new Categories($this->db);
        return $createCat->addCategory($parent, $desc);
    }

    /**
     *  Save edited category
     *
     *  @return JSON
     */
    public function edit()
    {
        if (!$this->ur->authorized('editcat')) {
            exit(ApiController::makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $cid = $this->up->cid;
        $desc = $this->up->catDesc;
        $editCat = new Categories($this->db);
        return $editCat->editCategory($cid, $desc);
    }

    /**
     *  Delete category
     *
     *  @return JSON
     */
    public function delete()
    {
        if (!$this->ur->authorized('deletecat')) {
            exit(ApiController::makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $cat = $this->up->cid;
        $deleteCat = new Categories($this->db);
        return $deleteCat->delCategory($cat);
    }

    /**
     *  Get the children of a category
     *
     *  @return JSON
     */
    public function getChildren()
    {
        return null;
    }
}
