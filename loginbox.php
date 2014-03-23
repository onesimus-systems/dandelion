<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
		<link rel="stylesheet" type="text/css" href="themes/<?php echo $theme;?>/main.css" />
		<title>Dandelion Web Log</title>
	</head>
	<body>
		<div id="login">
			<h1>Dandelion Web Log</h1>
            <?php echo $status; ?>
			<form name="login_form" action="scripts/authenticate.php" method="post">
				Username:<br /><input type="text" value="" name="in_name" autocomplete="off" autofocus /><br />
				Password:<br /><input type="password" value="" name="in_pass" /><br />
				<input type="submit" value="Login" id="login_button" />
			</form>
		</div>
	</body>
</html>