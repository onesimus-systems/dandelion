<?php
/**
 * User management page
 */
namespace Dandelion;

use \Dandelion\Utils\View;

$requiredCssFiles = array("jqueryui", "datetimepicker.css");
include $paths['app'].'/templates/includes/head.php';
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

<?php echo View::loadJS('jquery', 'jqueryui',  'userManager', 'userManagerForms', 'timepicker.min', 'slider'); ?>

<script type="text/javascript">
    userManager.init();
</script>

<!-- End Page Body -->

<?php include $paths['app'].'/templates/includes/footer.php'; ?>
