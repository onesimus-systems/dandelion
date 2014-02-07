<?php
include 'permset.php';
error_reporting(E_ALL);
ini_set('display_errors', True);

$loguid = isset($_POST['loguid']) ? vali($_POST['loguid']) : '';

$editloginfo = mysqli_query($con, 'SELECT * FROM log WHERE logid = "' . $loguid . '"');

$edit_log_info = mysqli_fetch_array($editloginfo);

echo json_encode($edit_log_info);