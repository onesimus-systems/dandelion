<?php
if ($realname != "Angie Martin") {
    while ($row = mysqli_fetch_array($grab_logs)) {
        echo '<form method="post">';
        echo '<div class="logentry">';
        echo '<h2>' . $row['title'] . '</h2>';
        echo '<p class="entry">' . nl2br($row['entry']) . '</p>';
        echo '<p class="entrymeta">Created by ' . $row['usercreated'] . ' on ' . $row['datec'] . ' @ ' . $row['timec'] . '. ';
        if ($row['edited']) { echo '(Edited)'; }
        echo '<br />Categorized as ' . $row['cat'] . '.';
        
        if ($user_info['realname'] == $row['usercreated'] OR ($user_info['realname'] == $row['usercreated'] AND $user_info['role'] == 'admin')) {
            ?>
                <input type="button" value="Edit" onclick="editFun.grabedit(<?php echo $row['logid']; ?>);" class="flri" />
            <?php
        }
        elseif ($user_info['role'] == 'admin') {
            ?>
                <input type="button" value="Edit as Admin" onclick="editFun.grabedit(<?php echo $row['logid']; ?>);" class="flri" />
            <?php
        }
        
        echo '</p></div></form>';
    }
}
else {
    echo "This account does not have permission to view the activity log.";
}