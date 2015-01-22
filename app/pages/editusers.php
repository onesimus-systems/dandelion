<?php
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

$requiredCssFiles = array("jqueryui", "datetimepicker.css");
include 'static/includes/head.php';
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

<?php echo loadJS('jquery', 'jqueryui',  'userManager.js', 'userManagerForms.js', 'timepicker.min.js', 'slider.js'); ?>

<script type="text/javascript">
    userManager.init();
</script>

<!-- End Page Body -->

<?php include 'static/includes/footer.php'; ?>
