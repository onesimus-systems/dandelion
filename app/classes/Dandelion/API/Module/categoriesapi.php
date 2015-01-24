<?php
/**
 * Handles authentication API requests
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date July 2014
 */
namespace Dandelion\API\Module;

use Dandelion\Controllers\ApiController;

class categoriesAPI extends BaseModule
{
    public function __construct($db, $ur, $params) {
        parent::__construct($db, $ur, $params);
    }

    /**
     * Add new category
     *
     * @return JSON
     */
    public function add() {
        if (!$this->ur->authorized('addcat')) {
            exit(makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $parent = $this->up->parentID;
        $desc = $this->up->catDesc;
        $createCat = new \Dandelion\Categories($this->db);
        return json_encode($createCat->addCategory($parent, $desc));
    }

    /**
     * Save edited category
     *
     * @return JSON
     */
    public function edit() {
        if (!$this->ur->authorized('editcat')) {
            exit(makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $cid = $this->up->cid;
        $desc = $this->up->catDesc;
        $editCat = new \Dandelion\Categories($this->db);
        return json_encode($editCat->editCategory($cid, $desc));
    }

    /**
     * Delete category
     *
     * @return JSON
     */
    public function delete() {
        if (!$this->ur->authorized('deletecat')) {
            exit(makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $cat = $this->up->cid;
        $deleteCat = new \Dandelion\Categories($this->db);
        return json_encode($deleteCat->delCategory($cat));
    }

    /**
     * Get the children of a category
     *
     * @return JSON
     */
    public function getChildren() {
        return NULL;
    }
}
