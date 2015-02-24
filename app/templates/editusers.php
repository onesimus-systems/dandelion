<?php
/**
 * User management page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['jqueryui', 'datetimepicker.min.css', 'editusers']]);
?>
<!-- Begin Page Body -->

<div id="editusers">
    <div id="dialogBox"></div>

    <form id="userManageForm">
        <div id="userlist"></div>
    </form>
</div>

<?= $this->loadJS(['jquery', 'jqueryui', 'common', 'userManager', 'userManagerForms', 'timepicker']) ?>
<script type="text/javascript">
    userManager.init();
</script>

<!-- End Page Body -->
