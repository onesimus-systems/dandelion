<?php
/**
 * Mailbox page
 */
namespace Dandelion;

$requiredCssFiles = array("jqueryui","mail");
include ROOT.'/pages/includes/head.php';
?>
<!-- Begin Page Body -->
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
      <a href="#" onClick="mail.writeMailDialog();"><img src="/assets/images/newmail.png" width="22" heigh="22" alt="New Mail" title="New Mail"></a>
      <a href="#" onClick="mail.getAllMail();"><img src="/assets/images/refresh.png" width="22" heigh="22" alt="Refresh" title="Refresh"></a>
      <a href="#" onClick="mail.replyToMail();"><img src="/assets/images/reply.png" width="22" heigh="22" alt="Reply" title="Reply"></a>
      <a href="#" onClick="mail.deleteMail();"><img src="/assets/images/maildelete.png" width="22" heigh="22" alt="Delete" title="Delete"></a>

      Folder: <select id="folder" onChange="mail.showFolder();">
          <option value="inbox">Inbox</option>
          <option value="trash">Trash</option>
      </select>

      <!--<a href="#" onClick="alert('Forward');"><img src="images/forward.png" width="22" heigh="22" alt="Forward" title="Forward"></a>-->
  </div>

  <div id="mailList"></div>
</div>

<?= loadJS("jquery", "jqueryui", "mail", "tinymce");?>
<!-- End Page Body -->
<?php include ROOT.'/pages/includes/footer.php'; ?>
