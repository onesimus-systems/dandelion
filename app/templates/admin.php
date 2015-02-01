<?php
/**
 * Administration dashboard
 */
// Stays false unless a button or admin control is shown
$content = false;
$this->layout('layouts::main', ['requiredCssFiles' => []]);
?>
<!-- Begin Page Body -->
<h2>Administration</h2>

<form name="admin_form">
	<?php
	if ($userRights->authorized(array('adduser', 'edituser', 'deleteuser'))) {
		echo '<input type="button" class="dButton adminButton" value="Manage Users" onClick="window.location=\'editusers\'"><br>';
		$content = true;
	}

	if ($userRights->authorized(array('addgroup', 'editgroup', 'deletegroup'))) {
		echo '<input type="button" class="dButton adminButton" value="Manage Groups" onClick="window.location=\'editgroups\'"><br>';
		$content = true;
	}

	if ($userRights->authorized(array('addcat', 'editcat', 'deletecat'))) {
		echo '<input type="button" class="dButton adminButton" value="Manage Categories" onClick="window.location=\'categories\'"><br>';
		$content = true;
	}
	?>
</form>
<!-- End Page Body -->

<?php
if (!$content) {
	echo 'Your account doesn\'t have rights to administrative controls.';
}
?>
