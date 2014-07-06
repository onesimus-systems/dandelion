<?php
/**
 * Full window for Cxeesto
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date March 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

include_once 'scripts/bootstrap.php';

if (!$_SESSION['app_settings']['cheesto_enabled'] || !$_SESSION['rights']['viewcheesto'] || !Gatekeeper\authenticated()) {
	header( 'Location: index.php' );
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
		<?php echo loadCssSheets("cheesto","cheestoWin"); ?>
		<title>Dandelion Presence</title>
	</head>
    
    <body onLoad="presence.checkstat(1);">
        <?php
		if ($_SESSION['app_settings']['cheesto_enabled'] && $_SESSION['rights']['viewcheesto']) {
		?>
        <div id="presence">
	        <h3><a href="#" onClick="window.close();" id="showHide">[ x ]</a> &#264;eesto:</h3>
            <?php
    		if ($_SESSION['rights']['updatecheesto']) {
    		?>
            <form method="post">
                <select id="cstatus">
                    <option>Set Status:</option>
                    <option>Available</option>
                    <option>Away From Desk</option>
                    <option>At Lunch</option>
                    <option>Out for Day</option>
                    <option>Out</option>
                    <option>Appointment</option>
                    <option>Do Not Disturb</option>
                    <option>Meeting</option>
                    <option>Out Sick</option>
                    <option>Vacation</option>
                </select>
                <input type="button" value="Set" class="linklike set" onClick="presence.setStatus(0);" />
            </form>
            <?php
    		}
            ?>
            
	        <div id="pt"></div>
        </div>
        <?php
		}
        ?>
        
        <?php echo loadJS("jquery","cheesto");?>
    </body>
</html>