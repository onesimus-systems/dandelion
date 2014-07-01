<?php
/**
 * Get mail count
 *
 * @author Lee Keitel
 * @date May, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

require_once '../bootstrap.php';

$myMail = new Mail\mail();

$count = $myMail->checkNewMail(true);

$count = array( 'count' => $count);

echo json_encode($count);