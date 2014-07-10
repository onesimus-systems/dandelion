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

require_once '../lib/bootstrap.php';

if (Gatekeeper\authenticated()) {
    $logs = new Logs();
    echo $logs->doAction($_POST);
}
