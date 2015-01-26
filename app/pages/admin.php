<?php
/**
 * Administration dashboard
 */
namespace Dandelion;

use \Dandelion\Utils\View;

// Stays false unless a button or admin control is shown
$content = false;
$requiredCssFiles = array("jqueryui");
include $paths['app'].'/pages/includes/head.php';
?>
<!-- Begin Page Body -->
<h2>Administration</h2>

<form name="admin_form" method="post" action="lib/admin_actions.php">
	<?php
	if ($User_Rights->authorized(array('adduser', 'edituser', 'deleteuser'))) {
		echo '<input type="button" class="dButton adminButton" value="Manage Users" onClick="window.location=\'editusers\'"><br>';
		$content = true;
	}

	if ($User_Rights->authorized(array('addgroup', 'editgroup', 'deletegroup'))) {
		echo '<input type="button" class="dButton adminButton" value="Manage Groups" onClick="window.location=\'editgroups\'"><br>';
		$content = true;
	}

	if ($User_Rights->authorized(array('addcat', 'editcat', 'deletecat'))) {
		echo '<input type="button" class="dButton adminButton" value="Manage Categories" onClick="window.location=\'categories\'"><br>';
		$content = true;
	}

	if ($User_Rights->authorized('admin')) {
		$content = true;
	?>
		<input type="button" class="dButton adminButton" value="Edit Site Slogan" onClick="adminAction.editSlogan('<?= addslashes($_SESSION['app_settings']['slogan']); ?>');"><br>
		<br><hr width="350"><br>

        Default theme:

    	<?= View::getThemeList('', false); ?>

        <input type="button" class="dButton" onClick="adminAction.saveDefaultTheme();" value="Save Theme"><br><br>

        &#264;eesto:
        <select id="cheesto_enabled">
            <option value="true" <?= CHEESTO_ENABLED ? 'selected' : ''; ?>>Enabled</option>
            <option value="false" <?= !CHEESTO_ENABLED ? 'selected' : ''; ?>>Disabled</option>
        </select>

        <input type="button" class="dButton" onClick="adminAction.saveCheesto();" value="Save"><br><br>

        Public API:
        <select id="api_enabled">
            <option value="true" <?= PUBLIC_API ? 'selected' : ''; ?>>Enabled</option>
            <option value="false" <?= !PUBLIC_API ? 'selected' : ''; ?>>Disabled</option>
        </select>

        <input type="button" class="dButton" onClick="adminAction.saveApiSetting();" value="Save">

        <br><br><hr width="350">
		<h3>Database Administration</h3>
		<input type="button" class="dButton adminButton" value="Export Database to File" onClick="adminAction.backupDB();" title="Creates a single file backup of the Dandelion database tables.&#013;If you have a lot of log entries, the file will be large."><br>
	<?php } ?>
</form>
<!-- End Page Body -->

<?php
if (!$content) {
	echo 'Your account doesn\'t have rights to administrative controls.';
}

echo View::loadJS('jquery','admin');

include $paths['app'].'/pages/includes/footer.php';
?>
