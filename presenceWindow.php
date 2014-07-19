<?php
/**
 * Full window for Cxeesto
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date March 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

require_once 'lib/bootstrap.php';

if (!$_SESSION['app_settings']['cheesto_enabled'] || !$User_Rights->authorized('viewcheesto') || !Gatekeeper\authenticated()) {
	header( 'Location: index.php' );
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
		<?php echo loadCssSheets("cheesto","cheestoWin"); ?>
		<title>Dandelion Presence</title>
	</head>
    
    <body onLoad="presence.checkstat(1);">
        <div id="presence">
	        <h3>&#264;eesto:</h3>
	        
        	<div id="mainPresence"></div>
        </div>
        
        <?php echo loadJS("jquery","cheesto");?>
    </body>
</html>