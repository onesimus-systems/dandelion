<?php
//This file is included on every page
//It initiates the login to the Dandelion database
$con = mysqli_connect("localhost","gardener","","");

if (mysqli_connect_errno($con))
    {
        $status = 'Database Status: <span class="bad">Not Connected</span><br />Failed to connect to MySQL: ' . mysqli_connect_error();
    }
else
    {
        $status = 'Database Status: <span class="good">Connected</span>';
    }
	
$cookie_name = "dandelionrememt";

function vali($data) {
    $data = trim($data);
    //$data = stripslashes($data);
    return $data;
}

function vali2($data) {
    $con2 = mysqli_connect("localhost","gardener","","");
    $data = mysqli_real_escape_string($con2, $data);
    mysqli_close($con2);
    return $data;
}
