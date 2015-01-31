<?php
/**
 * MySQL repo for categories
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class CategoriesRepo extends BaseMySqlRepo implements Interfaces\CategoriesRepo
{
    public function getAllCategories()
    {
        return $this->database->selectAll('category')->get();
    }

    public function addCategory($name, $pid)
    {
        $this->database->insert()
                       ->into($this->prefix.'category', ['description', 'pid'])
                       ->values([':name', ':pid']);

        return $this->database->go(['name' => $name, 'pid' => $pid]);
    }

    public function updateCategory($name, $cid)
    {
        $this->database->update($this->prefix.'category')
                       ->set('description = :name')
                       ->where('cid = :cid');

        return $this->database->go(['name' => $name, 'cid' => $cid]);
    }

    public function getCategoryParent($cid)
    {
        $this->database->select('pid')
                       ->from($this->prefix.'category')
                       ->where('cid = :cid');

        return $this->database->getFirst(['cid' => $cid])['pid'];
    }

    public function deleteCategory($cid)
    {
        $this->database->delete()
                       ->from($this->prefix.'category')
                       ->where('cid = :cid');

        return $this->database->go(['cid' => $cid]);
    }

    public function adoptChildren($pid, $cid)
    {
        $this->database->update($this->prefix.'category')
                       ->set('pid = :newp')
                       ->where('pid = :oldp');

        return $this->database->go(['newp' => $pid, 'oldp' => $cid]);
    }
}
