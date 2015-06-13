<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
  */
namespace Dandelion\Repos;

use \Dandelion\Repos\Interfaces;

class CategoriesRepo extends BaseRepo implements Interfaces\CategoriesRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'category';
    }

    public function getAllCategories()
    {
        return $this->database->find($this->table)->read();
    }

    public function getIdForCategoryWithParent($cat, $pid)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('parent', $pid)
            ->whereEqual('description', $cat)
            ->readField('id');
    }

    public function addCategory($name, $pid)
    {
        return $this->database->createItem($this->table, ['description' => $name, 'parent' => $pid]);
    }

    public function updateCategory($name, $cid)
    {
        return $this->database->updateItem($this->table, $cid, ['description' => $name]);
    }

    public function getCategoryParent($cid)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('id', $cid)
            ->readRecord('parent')['parent'];
    }

    public function deleteCategory($cid)
    {
        return $this->database->deleteItem($this->table, $cid);
    }

    public function adoptChildren($pid, $cid)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('parent', $cid)
            ->update(['parent' => $pid]);
    }
}
