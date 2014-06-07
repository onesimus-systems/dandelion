<?php
/**
 * This page is the heart of the whole application.
 * This page show the weblog and allows a user to search,
 * filter, add, or edit a log. This page also houses the
 * presence application Cxeesto.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 27, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

//Authenticate user, if fail go to login page
if (Gatekeeper\authenticated()) {
	if ($_SESSION['rights']['createlog']) {
		$add_link = '| <input type="button" class="dButton" onClick="addFun.showaddinputs();" value="Add New Log Entry" />';
	}
	else {
		$add_link = '';
	}
}
else {
	header( 'Location: index.php' );
}

/*
 * Notes:
 * Cxeesto is first called under mainScripts.js -> refreshLog
*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<?php echo loadCssSheets("cheesto","jqueryui"); ?>
		<title>Dandelion Web Log</title>
	</head>
    
	<body onLoad="refreshFun.startrefresh();">
	
		<?php
		if ($_SESSION['app_settings']['cheesto_enabled'] && $_SESSION['rights']['viewcheesto']) {
		?>
        <div id="presence">
	        <h3><a href="#" onClick="presence.showHideP();"><span id="showHide">[ - ]</span></a> &#264;eesto:</h3>
        	<div id="mainPresence">
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
        </div>
        <?php
		}
        ?>
    
        <div id="divMain">
            <header>
                <?php include 'scripts/header.php'; ?>
            </header>

            <?php
            if ($_SESSION['rights']['viewlog']) { ?>
                <div id="controlPanel">
                    <form id="category" method="post">
                        <input type="text" id="searchterm" size="40" value="Keyword" onClick="miscFun.clearval(this);" onKeyPress="return searchFun.check(event);" /><input type="text" id="datesearch" size="10" value="Date" onClick="miscFun.clearval(this);" />
                        <input type="button" value="Search Log" class="dButton" onClick="searchFun.searchlog();" /><br />
                        
                        Filter: 
                        <div id="categorySelects"></div>
                        
                        <input type="button" value="Filter" class="dButton" onClick="searchFun.filter('');" />
                        <?php echo $add_link; ?>
                    </form>
                    
                    <div id="add_edit" title="">
                        <form id="add_edit_form">
                            Title: <input type="text" id="logTitle" name="logTitle" value="" size="60"><br><br>
                            <textarea id="logEntry" name="logEntry" cols="80" rows="10"></textarea><br>
                            Category: <span id="catSpace"></span>
                            <div id="messages" style="display: none;"></div>
                        </form>
                    </div>
                    
                    <div id="dialog"></div>
                </div>
            <?php } ?>
            
            <div id="refreshed"></div>
            
            <footer>
                <?php include_once 'scripts/footer.php'; ?>
            </footer>
        </div>

        <?php echo loadJS("jquery","jqueryui","tinymce","catmanage","main","cheesto")?>
	</body>
</html>
