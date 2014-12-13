<?php
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

$showList = true;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="<?php echo FAVICON_PATH; ?>">
		<?php echo loadCssSheets("jqueryui","datetimepicker.css"); ?>
		<title>Dandelion Web Log</title>
	</head>
	<body>
        <header>
            <?php include 'views/header.php'; ?>
        </header>
        
		<?php include 'lib/editusersaction.php'; ?>
		
        <?php if ($showList) {?><br>
            <form method="post">
                Action: 
                <select name="user_action">
                    <option value="none">Select:</option>
                    
                    <?php
                    if ($User_Rights->authorized('adduser')) {
                        echo '<option value="add">Add User</option>';
                    }
                    
                    if ($User_Rights->authorized('deleteuser')) {
                        echo '<option value="delete">Delete</option>';
                    }
                    
                    if ($User_Rights->authorized('edituser')) {
                        echo '<option value="edit">Edit</option>';
                        echo '<option value="reset">Reset Password</option>';
                        echo '<option value="cxeesto">Change &#264;eesto</option>';
                        echo '<option value="revokeKey">Revoke API Key</option>';
                    }
                    ?>
                </select>
                
                <input type="submit" name="sub_type" value="Go">
                
                <br><br>
                
                <table id="main">
                    <tr>
                        <th>&nbsp;</th>
                        <th>Real Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Date Created</th>
                        <th>Theme</th>
                        <th>First Login</th>
                    </tr>
                    
                    <?php
                        // Database connection is defined in edituseractions.php
                        $allUsers = $conn->selectAll('users');
                        foreach ($allUsers as $row) {
                            echo '<tr>';
                            echo '<td><input type="radio" name="the_choosen_one" value="' . $row['userid'] . '"></td>';
                            echo '<td style="text-align: left;">' . $row['realname'] . '</td>';
                            echo '<td>' . $row['username'] . '</td>';
                            echo '<td>' . $row['role'] . '</td>';
                            echo '<td>' . $row['datecreated'] . '</td>';
                            echo '<td>' . $row['theme'] . '</td>';
                            echo '<td>' . $row['firsttime'] . '</td>';
                            echo '</tr>';
                        }
                    ?>
                </table>
            </form>
        <?php } ?>
        
        <footer>
            <?php include_once 'views/footer.php'; ?>
        </footer>
	</body>
</html>
