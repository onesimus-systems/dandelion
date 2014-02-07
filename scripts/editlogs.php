<?php
include 'dbconnect.php';

if (checkLogIn()) {
	if ($_SESSION['userInfo'][5] == "guest") {
		header( 'Location: viewlog.php' );
	}
	
	if ($_SESSION['userInfo'][5] === "admin") {
		$admin_link = '| <a href="admin.php">Administration</a>';
	}
	else {
		$admin_link = '';
	}
	
	if ($_SESSION['userInfo'][5] !== "guest") {
		$settings_link = '| <a href="settings.php">Settings</a>';
	}
	else {
		$settings_link = '';
	}
}
else {
	header( 'Location: index.php' );
}

$editedlog = isset($_POST['editlog']) ? $_POST['editlog'] : '';
$editedtitle = isset($_POST['edittitle']) ? $_POST['edittitle'] : '';
$choosen  = isset($_POST['choosen']) ? $_POST['choosen'] : '';

// Connect to DB
$db = new DB();
$conn = $db->dbConnect();

// Update the database
try {
    $stmt = $conn->prepare('UPDATE `log` SET `title` = :eTitle, `entry` = :eEntry, `edited` = 1 WHERE `logid` = :leLogID');
    $stmt->execute(array(
        'eTitle' => $editedtitle,
        'eEntry' => $editedlog,
        'leLogID' => $choosen
    ));
} catch(PDOExeception $e) {
    echo 'Error editing log.';
}