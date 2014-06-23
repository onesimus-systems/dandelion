<?php
/**
 * Get mailbox list of items
 *
 * @author Lee Keitel
 * @date May, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

require_once '../bootstrap.php';

$myMail = new Mail\mail();

$mailItems = $myMail->getMailList();

echo json_encode($mailItems);