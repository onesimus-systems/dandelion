<?php
/**
 * This page allows users to change their password
 * and change the number of logs show on the home page.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

include 'static/includes/head.php';
?>
<!-- Begin Page Body -->
<h2>User Settings</h2>

<?php require_once 'lib/saveSettings.php';?>

<form method="post">
    <input type="submit" name="set_action" class="dButton adminButton" value="Reset Password" />
    <br /><br /><hr width="350" /><br />
    
    How many logs do you want to see on the main page:<br />
    <input type="text" name="show_limit" size="3" value="<?php echo $_SESSION['userInfo']['showlimit']; ?>" />
    <input type="submit" name="set_action" class="dButton" value="Save Limit" />
</form>

<br /><hr width="350" /><br />

<form method="post">
	Current theme:
	
	<?php echo getThemeList(); ?>
	
    <input type="submit" name="set_action" class="dButton" value="Save Theme" />
</form>

<?php if ($_SESSION['app_settings']['public_api']) { ?>
    <br /><hr width="350" /><br />

    API Key: <span id="apiKey"></span>
    <br><br><input type="button" class="dButton" onClick="api.generateKey();" value="Generate New Key">
<?php
}

echo loadJS('jquery','settings.js'); ?>
<!-- End Page Body -->

<?php include 'static/includes/footer.php'; ?>
