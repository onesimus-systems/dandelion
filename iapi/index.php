<?php
/**
 * Central entry point for Dandelion's internal API.
 *
 * The DAPI array will always contain an error code. Please refer
 * to the documentation on the website or in the makeDAPI() function
 * for code meanings.
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
namespace Dandelion\API;

use Dandelion\Gatekeeper;

require_once '../scripts/bootstrap.php';

if (Gatekeeper\authenticated()) {
    $localCall = true;
    
    $url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
    $url = explode('/', $url);
    
    require ROOT.'/api/index.php';
    
    $returnObj = (array) json_decode(processRequest($_SESSION['userInfo']['userid'], $localCall, $url[0], $url[1], true));
    $returnObj['iapi'] = true;
    
    echo json_encode($returnObj);
}
else {
    exit('The internal API can only be accessed by Dandelion.');
}