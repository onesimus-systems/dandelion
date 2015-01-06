<?php
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

$showList = true;
include 'static/includes/head.php';
?>
<!-- Begin Page Body -->
<?php include 'lib/editusersaction.php'; ?>

<?php if ($showList) {?><br>
    <form id="userManageForm">        
        <button type="button" onClick="userManager.performAction();">Go</button>
        
        <br><br>
        
        <div id="userlist"></div>
    </form>
<?php }

echo loadJS("jquery", "userManager.js");?>

<script type="text/javascript">
    userManager.init();
</script>
<!-- End Page Body -->  
<?php include 'static/includes/footer.php'; ?>
