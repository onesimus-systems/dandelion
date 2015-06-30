<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
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
