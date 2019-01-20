<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['simpleCss' => ['jqueryui', 'datetimepicker', 'dashboard']]);
?>

<div id="elm" class="main-info"></div>

<script type="text/javascript">
    const props = {
        "showCreateBtn": <?= $showCreateButton ? 'true' : 'false' ?>,
        "showLog": <?= $showLog ? 'true' : 'false' ?>,
        "showCheesto": <?= $showCheesto ? 'true' : 'false' ?>,
        "cheestoEnabledClass": "<?= $showCheesto ? 'cheesto-enabled' : '' ?>"
    }
</script>

<?= $this->loadJS(['jquery', 'jqueryui', 'timepicker', 'dashboard']) ?>
