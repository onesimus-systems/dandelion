<?php

/**
 * Get mail count
 *
 * @author Lee Keitel
 *         @date May, 2014
 *        
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 *          *
 */
namespace Dandelion;

require_once '../bootstrap.php';

$myMail = new Mail\mail();

$piece = (array) json_decode($_REQUEST['mail']);

$response = $myMail->newMail($piece['subject'], $piece['body'], $piece['to'], $_SESSION['userInfo']['userid']);

echo $response;