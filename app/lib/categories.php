<?php
/**
 * Controller for category management
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date March 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 *
 */
namespace Dandelion;

// TODO: Create a better method to check these URLs
// Because this file is called from the root and a subdirectory,
// a check needs to be done to determine how the required files are
// included.
require_once (is_file('lib/bootstrap.php')) ? 'lib/bootstrap.php' : 'bootstrap.php';
require_once (is_file('classes/categories.php')) ? 'classes/categories.php' : '../classes/categories.php';

// Authenticate user, if fail go to login page
if (!Gatekeeper\authenticated()) {
    redirect('index');
}

$urlParams = new UrlParameters();

if ($urlParams->action == 'grabcats') {
    $past = json_decode(stripslashes($urlParams->pastSelections));
    $displayCats = new Categories(Storage\mySqlDatabase::getInstance());
    echo $displayCats->getChildren($past);
}

session_write_close();
