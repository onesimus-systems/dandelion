<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
  */
namespace Dandelion\API\Module;

use \Dandelion\Categories;
use \Dandelion\Exception\ApiException;
use \Dandelion\Controllers\ApiController;
use \Dandelion\Exception\ApiPermissionException;

class CategoriesAPI extends BaseModule
{
    /**
     *  Add new category
     *
     *  @return JSON
     */
    public function create()
    {
        if (!$this->ur->authorized('createcat')) {
            throw new ApiPermissionException();
        }

        $parent = $this->up->pid;
        $desc = $this->up->description;
        $createCat = new Categories($this->repo);

        if ($createCat->addCategory($parent, $desc)) {
            return 'Category created successfully';
        } else {
            throw new ApiException('Error creating category', 5);
        }
    }

    /**
     *  Save edited category
     *
     *  @return JSON
     */
    public function edit()
    {
        if (!$this->ur->authorized('editcat')) {
            throw new ApiPermissionException();
        }

        $cid = $this->up->cid;
        $desc = $this->up->description;
        $editCat = new Categories($this->repo);

        if ($editCat->editCategory($cid, $desc)) {
            return 'Category edited successfully';
        } else {
            throw new ApiException('Error editing category', 5);
        }
    }

    /**
     *  Delete category
     *
     *  @return JSON
     */
    public function delete()
    {
        if (!$this->ur->authorized('deletecat')) {
            throw new ApiPermissionException();
        }

        $cat = $this->up->cid;
        $deleteCat = new Categories($this->repo);

        if ($deleteCat->delCategory($cat)) {
            return 'Category deleted successfully';
        } else {
            throw new ApiException('Error deleting category', 5);
        }
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
