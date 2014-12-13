<?php
/**
 * This page is a tutorial that shows after a user
 * logs in to the first time and resets their password.
 * This page can also be accessed on-demand.
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

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="<?php echo FAVICON_PATH; ?>" />
		<?php echo loadCssSheets("tutorial"); ?>
		<title>Dandelion Web Log - Tutorial</title>
	</head>
	<body>
        <header>
            <?php include 'views/header.php'; ?>
        </header>
		
		<div id="content">
			<h2 class="t_cen">Tutorial</h2>
	        
	        <p class="le">Welcome to the Dandelion Web Log. This brief tutorial will walk you through how to do all the basic tasks in Dandelion.</p>
	        
	        <h3>Reset Password</h3>
	        <p class="le">To reset your password:
	            <ol>
	                <li>Click "Settings"</li>
	                <li>Click "Reset Password"</li>
	                <li>Enter new password twice</li>
	                <li>Click Reset</li>
	                <li>Dance</li>
	            </ol></p>
	            
	        <h3>Add a log entry</h3>
	        <p class="le">To add a log entry:
	            <ol>
	                <li>On the homepage, click "Add New Log entry" at the top of the page</li>
	                <li>Fill in the title, entry, and category</li>
	                <li>Click "Add Log"</li>
	            </ol></p>
	        
	        <h3>Edit a log entry</h3>
	        <p class="le">To edit a log entry:
	            <ol>
	                <li>Click "Edit" next to the entry you want to edit (you can only edit entries you entered)</li>
	                <li>Edit the text as needed</li>
	                <li>Click "Save Edit"</li>
	            </ol></p>
	            
	        <h3>Search Logs</h3>
	        <p class="le">To search for a log entry:
	            <ol>
	                <li>Type in keywords in the Keyword box and/or click the Date field to select a date</li>
	                <li>Click "Search Log"</li>
	                <li>Click "Clear Search" to return to the live log</li>
	            </ol></p>
	            
	        <h3>Filter Logs by Category</h3>
	        <p class="le">To filter the log:
	            <ol>
	                <li>Select the desired category by choosing from the drop down boxes next to Filter</li>
	                <li>Click "Filter"</li>
	                <li>Click "Clear Filter" to return to the live log</li>
	            </ol></p>
	            
	        <h3>Using &#264;eesto</h3>
	        <p class="le">To set your current status:
	            <ol>
	                <li>Select your status from the dropdown under Presence</li>
	                <li>Click "Set"</li>
	                <li>Enter any extra data as needed</li>
	            </ol></p>
	            
	        <p class="le">To see someone's status:
	            <ol>
	                <li>Hover your mouse over the status icon to view its meaning</li>
	                <li>If the person is set as Away or Out For the Day, their return time will also be shown</li>
	            </ol></p>
	            
	        <p class="le">To see someone's message:
	            <ol>
	                <li>Hover your mouse over the person's name to view any message they left</li>
	            </ol></p>
        </div>
        
        <footer>
            <?php include_once 'views/footer.php'; ?>
        </footer>
	</body>
</html>
