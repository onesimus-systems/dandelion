<?php
/**
 * Repo interfaces for Categories
 */
namespace Dandelion\Repos\Interfaces;

interface CategoriesRepo
{
    public function getAllCategories();
    public function getIdForCategoryWithParent($cat, $pid);
    public function addCategory($name, $pid);
    public function updateCategory($name, $cid);
    public function getCategoryParent($cid);
    public function deleteCategory($cid);
    public function adoptChildren($pid, $cid);
}
