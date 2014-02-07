<?php
include 'permset.php';
error_reporting(E_ALL);
ini_set('display_errors', True);

$editedlog = isset($_POST['editlog']) ? vali($_POST['editlog']) : '';
$editedtitle = isset($_POST['edittitle']) ? vali($_POST['edittitle']) : '';
$choosen  = isset($_POST['choosen']) ? vali($_POST['choosen']) : '';

//Update log entry
if (!mysqli_query($con, 'UPDATE log SET title = "'.$editedtitle.'", entry = "'.$editedlog.'", edited = 1 WHERE logid = "'.$choosen.'"')){
	die('Error saving log: ' . mysqli_error($con));
}
else {
	echo "Log updated";
}