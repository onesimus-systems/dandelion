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

use Dandelion\API\ApiController;

if (REQ_SOURCE != 'api' && REQ_SOURCE != 'iapi') {
    exit(makeDAPI(2, 'This script can only be called by the API.', 'categories'));
}

class categoriesAPI {
    /**
     * Add new category
     *
     * @return JSON
     */
    public static function add($db, $ur, $params) {
        if (!$ur->authorized('addcat')) {
            exit(makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $parent = $params->parentID;
        $desc = $params->catDesc;
        $createCat = new \Dandelion\Categories($db);
        return json_encode($createCat->addCategory($parent, $desc));
    }

    /**
     * Save edited category
     *
     * @return JSON
     */
    public static function edit($db, $ur, $params) {
        if (!$ur->authorized('editcat')) {
            exit(makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $cid = $params->cid;
        $desc = $params->catDesc;
        $editCat = new \Dandelion\Categories($db);
        return json_encode($editCat->editCategory($cid, $desc));
    }

    /**
     * Delete category
     *
     * @return JSON
     */
    public static function delete($db, $ur, $params) {
        if (!$ur->authorized('deletecat')) {
            exit(makeDAPI(4, 'Your account doesn\'t have permissions to add a category.', 'categories'));
        }

        $cat = $params->cid;
        $deleteCat = new \Dandelion\Categories($db);
        return json_encode($deleteCat->delCategory($cat));
    }

    /**
     * Get the children of a category
     *
     * @return JSON
     */
    public static function getChildren($db, $ur, $params) {
        return NULL;
    }
}
