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

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

if ($User_Rights->authorized('createlog')) {
	$add_link = '| <input type="button" class="dButton" onClick="addFun.showaddinputs();" value="Add New Log Entry" />';
}
else {
	$add_link = '';
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
        <link rel="icon" type="image/ico" href="<?php echo FAVICON_PATH; ?>" />
		<?php echo loadCssSheets("cheesto","jqueryui"); ?>
		<title>Dandelion Web Log</title>
	</head>
    
	<body onLoad="refreshFun.startrefresh(); presence.checkstat(0); mail.areUnread();">
	
		<?php
		if ($_SESSION['app_settings']['cheesto_enabled'] && $User_Rights->authorized('viewcheesto')) {
		?>
        <div id="presence">
	        <h3><a href="#" onClick="presence.showHideP();"><span id="showHide">[ - ]</span></a> &#264;eesto: <a href="mail"><img id="mailicon" src="static/images/nomail.png" width="32" height="16" alt="No Mail"></a></h3>
	        
        	<div id="mainPresence"></div>
        </div>
        <?php
		}
        ?>
    
        <div id="divMain">
            <header>
                <?php include 'views/header.php'; ?>
            </header>

            <?php
            if ($User_Rights->authorized('viewlog')) { ?>
                <div id="controlPanel">
                    <form id="category" method="post">
                        <input type="text" id="searchterm" size="40" value="Keyword" onClick="this.value='';" onKeyPress="return searchFun.check(event);" /><input type="text" id="datesearch" size="10" value="Date" onClick="this.value='';" />
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
            
                <div id="refreshed"></div>
            <?php } ?>
            
            <footer>
                <?php include_once 'views/footer.php'; ?>
            </footer>
        </div>

        <?php echo loadJS("jquery","jqueryui","tinymce","catmanage","main","cheesto","mail.js")?>
	</body>
</html>
