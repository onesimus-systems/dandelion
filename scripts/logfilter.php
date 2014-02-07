<?php
include 'permguest.php';

$filter_1 = isset($_POST['f_cat_1']) ? vali($_POST['f_cat_1']) : '';
$filter_2 = isset($_POST['f_cat_2']) ? vali($_POST['f_cat_2']) : '';
$filter_3 = isset($_POST['f_cat_3']) ? vali($_POST['f_cat_3']) : '';
$filter_4 = isset($_POST['f_cat_4']) ? vali($_POST['f_cat_4']) : '';
$filter_5 = isset($_POST['f_cat_5']) ? vali($_POST['f_cat_5']) : '';
$keyw = isset($_POST['keyw']) ? vali($_POST['keyw']) : '';
$dates = isset($_POST['dates']) ? vali($_POST['dates']) : '';
$type = isset($_POST['type']) ? vali($_POST['type']) : '';

if ($type == "") {
    $filter = $filter_1;
    $cat_stop = 0;

    if ($filter_2 != "" AND $filter_2 != "Select:" AND $cat_stop == 0) {
        $filter = $filter . ":" . $filter_2;
    }
    else {
        $cat_stop = 1;
    }
    if ($filter_3 != "" AND $filter_3 != "Select:" AND $cat_stop == 0) {
        $filter = $filter . ":" . $filter_3;
    }
    else {
        $cat_stop = 1;
    }
    if ($filter_4 != "" AND $filter_4 != "Select:" AND $cat_stop == 0) {
        $filter = $filter . ":" . $filter_4;
    }
    else {
        $cat_stop = 1;
    }
    if ($filter_5 != "" AND $filter_5 != "Select:" AND $cat_stop == 0) {
        $filter = $filter . ":" . $filter_5;
    }

    ?>
    <form>
        <h3>**Filter applied: <?php echo $filter; ?>**</h3>
        <input type="button" value="Clear Filter" onClick="refreshLog('clearf')" />
    </form>

    <?php
    $grab_logs = mysqli_query($con, 'SELECT * FROM log WHERE cat LIKE "%'.$filter.'%" ORDER BY logid DESC');
}

else {
    if ($type == "keyw") {
        $message = $keyw;
        
        $grab_logs = mysqli_query($con, 'SELECT * FROM log WHERE title LIKE "%'.$keyw.'%" or entry LIKE "%'.$keyw.'%" ORDER BY logid DESC');
    }
    else if ($type == "dates") {
        $message = $dates;
        
        $grab_logs = mysqli_query($con, 'SELECT * FROM log WHERE datec="'.$dates.'" ORDER BY logid DESC');
    }
    else if ($type == "both") {
        $message = $keyw.' on '.$dates;
        
        $grab_logs = mysqli_query($con, 'SELECT * FROM log WHERE (title LIKE "%'.$keyw.'%" or entry LIKE "%'.$keyw.'%") and datec="'.$dates.'" ORDER BY logid DESC');
    }
    ?>
    
    <form>
        <h3 style="display:inline;">Search results for: <?php echo $message; ?></h3>
        <input type="button" value="Clear Search" onClick="refreshLog('clearf')" />
    </form>
    
    <?php
}

include 'readlog.php';