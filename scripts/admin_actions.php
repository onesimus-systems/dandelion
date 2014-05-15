<?php
/**
 * This script forwards admin actions to adminActions class
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

include 'grabber.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo adminActions::doAction($_POST);
}