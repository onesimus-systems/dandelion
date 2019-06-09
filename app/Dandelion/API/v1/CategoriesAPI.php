<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\v1;

use Dandelion\Categories;
use Dandelion\API\ApiCommander;
use Dandelion\API\BaseModule;
use Dandelion\Exception\ApiException;
use Dandelion\Controllers\ApiController;
use Dandelion\Exception\ApiPermissionException;

class CategoriesAPI extends BaseModule
{
    /**
     *  Add new category
     *
     *  @return JSON
     */
    public function create($params)
    {
        if (!$this->authorized($this->requestUser, 'create_cat')) {
            throw new ApiPermissionException();
        }

        $createCat = new Categories($this->repo);

        if ($createCat->addCategory($params->pid, $params->description)) {
            return 'Category created successfully';
        } else {
            throw new ApiException('Error creating category', ApiCommander::API_GENERAL_ERROR);
        }
    }

    /**
     *  Save edited category
     *
     *  @return JSON
     */
    public function edit($params)
    {
        if (!$this->authorized($this->requestUser, 'edit_cat')) {
            throw new ApiPermissionException();
        }

        $editCat = new Categories($this->repo);

        if ($editCat->editCategory($params->cid, $params->description)) {
            return 'Category edited successfully';
        } else {
            throw new ApiException('Error editing category', ApiCommander::API_GENERAL_ERROR);
        }
    }

    /**
     *  Delete category
     *
     *  @return JSON
     */
    public function delete($params)
    {
        if (!$this->authorized($this->requestUser, 'delete_cat')) {
            throw new ApiPermissionException();
        }

        $deleteCat = new Categories($this->repo);

        if ($deleteCat->delCategory($params->cid)) {
            return 'Category deleted successfully';
        } else {
            throw new ApiException('Error deleting category', ApiCommander::API_GENERAL_ERROR);
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
