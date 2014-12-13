<?php
/**
 * Allows a user to view, send, and manage their mailbox
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

require_once 'lib/bootstrap.php';

if (!Gatekeeper\authenticated()) {
	redirect('index');
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="static/images/favicon.ico" />
		<?php echo loadCssSheets("mail", "jqueryui"); ?>
		<title>Dandelion Web Log</title>
	</head>
	
	<body>
        <header>
            <?php include 'views/header.php'; ?>
        </header>
		
		<div id="mailDialog" title="View Mail"></div>
		
		<div id="writeMail" title="">
            <form id="mailForm">
                To: <input id="toUsers"><br>
                Subject: <input type="text" id="mailSubject" name="mailSubject" value="" size="60"><br><br>
                <textarea id="mailBody" name="mailBody" cols="80" rows="10"></textarea><br>
                <div id="messages" style="display: none;"></div>
                <input type="hidden" id="toUsersId" value="">
            </form>
        </div>
		
		<h2>Mail Box</h2>
		
		<div id="mailbox">
		  <div id="controls">
		      <a href="#" onClick="mail.writeMailDialog();"><img src="static/images/newmail.png" width="22" heigh="22" alt="New Mail" title="New Mail"></a>
		      <a href="#" onClick="mail.getAllMail();"><img src="static/images/refresh.png" width="22" heigh="22" alt="Refresh" title="Refresh"></a>
		      <a href="#" onClick="mail.replyToMail();"><img src="static/images/reply.png" width="22" heigh="22" alt="Reply" title="Reply"></a>
		      <a href="#" onClick="mail.deleteMail();"><img src="static/images/maildelete.png" width="22" heigh="22" alt="Delete" title="Delete"></a>
		      
		      Folder: <select id="folder" onChange="mail.showFolder();">
		          <option value="inbox">Inbox</option>
		          <option value="trash">Trash</option>
		      </select>
		      
		      <!--<a href="#" onClick="alert('Forward');"><img src="images/forward.png" width="22" heigh="22" alt="Forward" title="Forward"></a>-->
		  </div>
		  
		  <div id="mailList"></div>
		</div>
        
        <footer>
            <?php include_once 'views/footer.php'; ?>
        </footer>
	</body>
	
	<?php echo loadJS("jquery", "jqueryui", "mail.js", "tinymce");?>
</html>
