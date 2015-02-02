<?php
/**
 * User management page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ["jqueryui", "datetimepicker.min.css"]]);
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

<?= $this->loadJS(['jquery', 'jqueryui',  'userManager', 'userManagerForms', 'timepicker']) ?>
<script type="text/javascript">
    userManager.init();
</script>

<!-- End Page Body -->
