<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <?= $this->getCssSheets(['cheesto','cheestowin']) ?>
		<title>Dandelion Presence</title>
	</head>

    <body onLoad="presence.checkstat(1);">
        <div id="presence">
	        <h3>&#264;eesto:</h3>

        	<div id="mainPresence"></div>
        </div>

        <?= $this->loadJS(['jquery','cheesto']) ?>
    </body>
</html>
