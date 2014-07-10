<?php
/**
 * This script forwards admin actions to adminActions class
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

require_once '../lib/bootstrap.php';

if ($_SESSION['rights']['admin']){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = new adminActions();
        echo $action->doAction($_POST);
    }
}