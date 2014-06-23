<?php
/**
 * Allows a user to view, send, and manage their mailbox
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

require_once 'scripts/bootstrap.php';

if (!Gatekeeper\authenticated()) {
	header( 'Location: index.php' );
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<?php echo loadCssSheets("mail", "jqueryui"); ?>
		<title>Dandelion Web Log</title>
	</head>
	
	<body onload="mail.getAllMail();">
        <header>
            <?php include 'scripts/header.php'; ?>
        </header>
		
		<div id="mailDialog" title="View Mail"></div>
		
		<h2>Mail Box</h2>
		
		<div id="mailbox">
		  <div id="controls">
		      <!-- TODO: Create mail controls (delete, reply, forward, new) -->
		  </div>
		  
		  <div id="mailList"></div>
		</div>
        
        <footer>
            <?php include_once 'scripts/footer.php'; ?>
        </footer>
	</body>
	
	<?php echo loadJS("jquery", "jqueryui", "mail.js");?>
</html>
