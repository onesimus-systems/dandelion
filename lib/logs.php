<?php
/**
  * Handles all requests pertaining to log entries
  *
  * @author Lee Keitel
  * @date May 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

require_once 'bootstrap.php';

if (Gatekeeper\authenticated()) {
    header('Dandelion Message: Move to logs API'); // To track if anything is still calling this file while the frontend is migrated
    $logs = new Logs();
    echo $logs->doAction($_POST);
}
