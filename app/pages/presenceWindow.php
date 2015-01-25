<?php
/**
 * Windowed version of Cheesto
 */
namespace Dandelion;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
		<?= loadCssSheets("cheesto","cheestoWin"); ?>
		<title>Dandelion Presence</title>
	</head>

    <body onLoad="presence.checkstat(1);">
        <div id="presence">
	        <h3>&#264;eesto:</h3>

        	<div id="mainPresence"></div>
        </div>

        <?= loadJS("jquery","cheesto");?>
    </body>
</html>
