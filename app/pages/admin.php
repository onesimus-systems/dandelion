<?php
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

// Stays false unless a button or admin control is shown
$content = false;
$requiredCssFiles = array("jqueryui");
include 'static/includes/head.php';
?>
<!-- Begin Page Body -->
<h2>Administration</h2>

<form name="admin_form" method="post" action="lib/admin_actions.php">
	<?php
	if ($User_Rights->authorized('adduser') || $User_Rights->authorized('edituser') || $User_Rights->authorized('deleteuser')) {
		echo '<input type="button" class="dButton adminButton" value="Manage Users" onClick="window.location=\'editusers\'"><br>';
		$content = true;
	}
	
	if ($User_Rights->authorized('addgroup') || $User_Rights->authorized('editgroup') || $User_Rights->authorized('deletegroup')) {
		echo '<input type="button" class="dButton adminButton" value="Manage Groups" onClick="window.location=\'editgroups\'"><br>';
		$content = true;
	}
	
	if ($User_Rights->authorized('addcat') || $User_Rights->authorized('editcat') || $User_Rights->authorized('deletecat')) {
		echo '<input type="button" class="dButton adminButton" value="Manage Categories" onClick="window.location=\'categories\'"><br>';
		$content = true;
	}
	
	if ($User_Rights->authorized('admin')) {
		$content = true;
	?>
		<input type="button" class="dButton adminButton" value="Edit Site Slogan" onClick="adminAction.editSlogan('<?php echo addslashes($_SESSION['app_settings']['slogan']); ?>');"><br>
		<br><hr width="350"><br>

        Default theme:
    	
    	<?php echo getThemeList('', false); ?>
    	
        <input type="button" class="dButton" onClick="adminAction.saveDefaultTheme();" value="Save Theme"><br><br>
        
        &#264;eesto:
        <select id="cheesto_enabled">
            <option value="true" <?php echo $_SESSION['app_settings']['cheesto_enabled'] ? 'selected' : ''; ?>>Enabled</option>
            <option value="false" <?php echo !$_SESSION['app_settings']['cheesto_enabled'] ? 'selected' : ''; ?>>Disabled</option>
        </select>
        
        <input type="button" class="dButton" onClick="adminAction.saveCheesto();" value="Save"><br><br>
        
        Public API:
        <select id="api_enabled">
            <option value="true" <?php echo $_SESSION['app_settings']['public_api'] ? 'selected' : ''; ?>>Enabled</option>
            <option value="false" <?php echo !$_SESSION['app_settings']['public_api'] ? 'selected' : ''; ?>>Disabled</option>
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

echo loadJS('jquery','admin.js');

include 'static/includes/footer.php';
?>
