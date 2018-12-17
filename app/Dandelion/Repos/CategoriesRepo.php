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

use Dandelion\Repos\Interfaces;

class CategoriesRepo extends BaseRepo implements Interfaces\CategoriesRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'category';
    }

    private function fixCategoryFieldTypes(&$cat)
    {
        $cat['id'] = (int) $cat['id'];
    }

    public function getAllCategories()
    {
        $cats = $this->database
            ->find($this->table)
            ->orderAsc($this->table.'.description')
            ->read();

        foreach ($cats as &$cat) {
            $this->fixCategoryFieldTypes($cat);
        }

        return $cats;
    }

    public function getIdForCategoryWithParent($cat, $pid)
    {
        return (int) $this->database
            ->find($this->table)
            ->whereEqual('parent', $pid)
            ->whereEqual('description', $cat)
            ->readField('id');
    }

    public function addCategory($name, $pid)
    {
        return $this->database
            ->createItem($this->table, ['description' => $name, 'parent' => $pid]);
    }

    public function updateCategory($name, $cid)
    {
        return $this->database
            ->updateItem($this->table, $cid, ['description' => $name]);
    }

    public function getCategoryParent($cid)
    {
        $parent = $this->database
            ->find($this->table)
            ->whereEqual('id', $cid)
            ->readRecord('parent')['parent'];
        $this->fixCategoryFieldTypes($parent);
        return $parent;
    }

    public function deleteCategory($cid)
    {
        return $this->database
            ->deleteItem($this->table, $cid);
    }

    public function adoptChildren($pid, $cid)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('parent', $cid)
            ->update(['parent' => $pid]);
    }
}
