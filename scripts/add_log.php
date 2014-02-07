<?php
include 'permset.php';

if ($realname != "Angie Martin") {
    $new_title = isset($_POST['add_title']) ? vali2($_POST['add_title']) : '';
    $new_entry = isset($_POST['add_entry']) ? vali2($_POST['add_entry']) : '';
    $new_category_1 = isset($_POST['cat_1']) ? vali2($_POST['cat_1']) : '';
    $new_category_2 = isset($_POST['cat_2']) ? vali2($_POST['cat_2']) : '';
    $new_category_3 = isset($_POST['cat_3']) ? vali2($_POST['cat_3']) : '';
    $new_category_4 = isset($_POST['cat_4']) ? vali2($_POST['cat_4']) : '';
    $new_category_5 = isset($_POST['cat_5']) ? vali2($_POST['cat_5']) : '';

    $new_cat = $new_category_1;
    $cat_stop = 0;

    if ($new_title != NULL AND $new_title != "" AND $new_entry != NULL AND $new_entry != "" AND $new_category_1 != NULL AND $new_category_1 != "select") {
        if ($new_category_2 != "" AND $new_category_2 != "Select:" AND $cat_stop == 0) {
            $new_cat = $new_cat . ":" . $new_category_2;
        }
        else {
            $cat_stop = 1;
        }
        if ($new_category_3 != "" AND $new_category_3 != "Select:" AND $cat_stop == 0) {
            $new_cat = $new_cat . ":" . $new_category_3;
        }
        else {
            $cat_stop = 1;
        }
        if ($new_category_4 != "" AND $new_category_4 != "Select:" AND $cat_stop == 0) {
            $new_cat = $new_cat . ":" . $new_category_4;
        }
        else {
            $cat_stop = 1;
        }
        if ($new_category_5 != "" AND $new_category_5 != "Select:" AND $cat_stop == 0) {
            $new_cat = $new_cat . ":" . $new_category_5;
        }
        
        $datetime = getdate();
        $new_date = $datetime['year'] . '-' . $datetime['mon'] . '-' . $datetime['mday'];
        $new_time = $datetime['hours'] . ':' . $datetime['minutes'] . ':' . $datetime['seconds'];

        //Add new log entry
        $qu = 'INSERT INTO log (logid, datec, timec, title, entry, usercreated, cat)  VALUES ("", "'.$new_date.'", "'.$new_time.'", "'.$new_title.'", "'.$new_entry.'", "'.$realname.'", "'.$new_cat.'")';
        
        if (!mysqli_query($con, $qu)) {
            die('Error creating entry: ' . mysqli_error($con));
        }
        
        echo "Log added";
    }
    else {
        echo '<span class="bad">Log entries must have a valid title, category, and entry text.</span>';
    }
}