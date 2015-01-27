<?php
/**
 * Administration dashboard
 */
// Stays false unless a button or admin control is shown
$content = false;
$this->layout('layouts::main', ['requiredCssFiles' => ['jqueryui']]);
?>
<!-- Begin Page Body -->
<h2>Administration</h2>

<form name="admin_form" method="post" action="lib/admin_actions.php">
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

	if ($userRights->authorized('admin')):
		$content = true;
	?>
		<br><hr width="350">
		<h3>Database Administration</h3>
		<input type="button" class="dButton adminButton" value="Export Database to File" onClick="adminAction.backupDB();" title="Creates a single file backup of the Dandelion database tables.&#013;If you have a lot of log entries, the file will be large."><br>
	<?php endif; ?>
</form>
<!-- End Page Body -->

<?php
if (!$content) {
	echo 'Your account doesn\'t have rights to administrative controls.';
}
?>

<?= $this->loadJS(['jquery','admin']) ?>
