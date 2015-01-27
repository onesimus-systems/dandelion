<?php
/**
 * Dashboard page
 */
namespace Dandelion;

use \Dandelion\Utils\View;

$addLink = '';
if ($User_Rights->authorized('createlog')) {
	$addLink = '| <input type="button" class="dButton" onClick="addFun.showaddinputs();" value="Add New Log Entry" />';
}

$requiredCssFiles = array("cheesto","jqueryui",);
include $paths['app'].'/templates/includes/head.php';
?>
<!-- Begin Page Body -->
<div id="divMain">
    <?php
    if ($cheestoEnabled && $User_Rights->authorized('viewcheesto')) {
    ?>
    <div id="presence">
        <h3><a href="#" onClick="presence.showHideP();"><span id="showHide">[ - ]</span></a> &#264;eesto: <a href="mail"><img id="mailicon" src="/assets/images/nomail.png" width="32" height="16" alt="No Mail"></a></h3>

    	<div id="mainPresence"></div>
    </div>
    <?php
    }

    if ($User_Rights->authorized('viewlog')) { ?>
        <div id="controlPanel">
            <form id="category" method="post">
                <input type="text" id="searchterm" size="40" value="Keyword" onClick="this.value='';" onKeyPress="return searchFun.check(event);" /><input type="text" id="datesearch" size="10" value="Date" onClick="this.value='';" />
                <input type="button" value="Search Log" class="dButton" onClick="searchFun.searchlog();" /><br />

                Filter:
                <div id="categorySelects"></div>

                <input type="button" value="Filter" class="dButton" onClick="searchFun.filter('');" />
                <?php echo $addLink; ?>
            </form>

            <div id="add_edit" title="">
                <form id="add_edit_form">
                    Title: <input type="text" id="logTitle" name="logTitle" value="" size="60"><br><br>
                    <textarea id="logEntry" name="logEntry" cols="80" rows="10"></textarea><br>
                    Category: <span id="catSpace"></span>
                    <div id="messages" style="display: none;"></div>
                </form>
            </div>

            <div id="dialog"></div>
        </div>

        <div id="refreshed"></div>
    <?php } ?>
</div>

<?php echo View::loadJS("jquery","jqueryui","tinymce","cheesto","catManage","logs","mail")?>
<script type="text/javascript">
    refreshFun.runFirst();
    presence.checkstat(0);
    mail.areUnread();
    $('#presence').draggable();
</script>
<!-- End Page Body -->
<?php include $paths['app'].'/templates/includes/footer.php'; ?>
