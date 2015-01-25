<?php
/**
 * User management page
 */
namespace Dandelion;

$requiredCssFiles = array("jqueryui", "datetimepicker.css");
include ROOT.'/pages/includes/head.php';
?>
<!-- Begin Page Body -->

<div id="editusers">
    <div id="dialog"></div>

    <form id="userManageForm">
        <button type="button" onClick="userManager.performAction();">Go</button>

        <br><br>

        <div id="userlist"></div>
    </form>
</div>

<?php echo loadJS('jquery', 'jqueryui',  'userManager', 'userManagerForms', 'timepicker.min', 'slider'); ?>

<script type="text/javascript">
    userManager.init();
</script>

<!-- End Page Body -->

<?php include ROOT.'/pages/includes/footer.php'; ?>
