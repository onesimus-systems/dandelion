<?php
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

$showList = true;
include 'static/includes/head.php';
?>
<!-- Begin Page Body -->
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
<!-- End Page Body -->  
<?php include 'static/includes/footer.php'; ?>
