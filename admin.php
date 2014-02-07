<!DOCTYPE html>

<?php include 'scripts/permadmin.php'; ?>

<html>
	<head>
		<meta charset="utf-8" />
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
		<title>Dandelion Web Log</title>
	</head>
	<body>
        <header>
            <?php include 'scripts/header.php'; ?>
        </header>
		
        <h3>Administration</h3>
		
        <form name="admin_form" method="post" action="scripts/admin_actions.php">
			<input type="submit" name="sub_action" value="Set New Features" /><br />
			<input type="submit" name="sub_action" value="Clear Session Tokens" /><br />
			<input type="submit" name="sub_action" value="Backup Database" /><br />
			<input type="submit" name="sub_action" value="Optimize Database" /><br />
        </form>
        
        <br /><a href="editusers.php">Edit Users</a>
        
        <footer>
            <p id="credits">&copy; 2013 Daedalus Computer Services</p>
        </footer>
	</body>
</html>
