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

require_once 'bootstrap.php';

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
