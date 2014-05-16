<?php
/**
  * Handles all requests pertaining to log entries
  *
  * @author Lee Keitel
  * @date May 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

require_once 'grabber.php';

if (authenticated()) {
    echo Logs::doAction($_POST);
}
