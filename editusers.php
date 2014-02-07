<!DOCTYPE html>

<?php include 'scripts/permadmin.php'; ?>

<html>
	<head>
		<meta charset="utf-8" />
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
		<link rel="stylesheet" type="text/css" href="jquery/css/smoothness/jquery-ui.min.css" />
		<title>Dandelion Web Log</title>
	</head>
	<body>
        <header>
            <?php include 'scripts/header.php'; ?>
        </header>
        
		<?php include 'scripts/editusersaction.php'; ?>
		
        <form method="post">
		Action: 
		<select name="user_action2">
			<option value="none">Select:</option>
			<option value="cxeesto">Change &#264;eesto</option>
			<option value="delete">Delete</option>
			<option value="edit">Edit</option>
			<option value="reset">Reset Password</option>
			<option value="add">Add User</option>
		</select>
		
		<input type="submit" name="sub_type" value="Go" />
        
		<br /><br />
		
        <table id="main">
			<tr>
				<th>Choose:</th>
				<th>User ID</th>
				<th>Username</th>
				<th>Real Name</th>
				<th>Settings ID</th>
				<th>Role</th>
				<th>Date Created</th>
				<th>First Login</th>
			</tr>
			
			<?php
				$grab_users = mysqli_query($con, "SELECT * FROM users");
				
				while ($row = mysqli_fetch_array($grab_users)) {
					echo '<tr>';
					echo '<td><input type="radio" name="the_choosen_one" value="' . $row['userid'] . '" /></td>';
					echo '<td>' . $row['userid'] . '</td>';
					echo '<td>' . $row['username'] . '</td>';
					echo '<td>' . $row['realname'] . '</td>';
					echo '<td>' . $row['settings_id'] . '</td>';
					echo '<td>' . $row['role'] . '</td>';
					echo '<td>' . $row['datecreated'] . '</td>';
					echo '<td>' . $row['firsttime'] . '</td>';
					echo '</tr>';
				}
			?>
		</table>
		<br />
        
		Action: 
		<select name="user_action">
			<option value="none">Select:</option>
			<option value="cxeesto">Change &#264;eesto</option>
			<option value="delete">Delete</option>
			<option value="edit">Edit</option>
			<option value="reset">Reset Password</option>
			<option value="add">Add User</option>
		</select>
		
		<input type="submit" name="sub_type" value="Go" />
        </form>
        
        
        
        <footer>
            <p id="credits">&copy; 2013 Daedalus Computer Services</p>
        </footer>
	</body>
</html>
