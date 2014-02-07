<?php
include 'permguest.php';

$grab_logs = mysqli_query($con, "SELECT * FROM log ORDER BY logid DESC LIMIT ".$user_info['showlimit']."");
	
include 'readlog.php';