<?php
/**
 * Category management page
 */
namespace Dandelion;

use \Dandelion\Utils\View;

$requiredCssFiles = array();
include $paths['app'].'/pages/includes/head.php';
?>
<!-- Begin Page Body -->
<h3>Category Management</h3>

<div id="message"></div>

<form method="post">
	<div name="categorySelects" id="categorySelects"></div><br><br>

	<?php
	if ($User_Rights->authorized('addcat')) {
		echo '<input type="button" class="dButton" onClick="CategoryManage.createNew();" value="Add Category">';
	}

	if ($User_Rights->authorized('editcat')) {
		echo '<input type="button" class="dButton" onClick="CategoryManage.editCat();" value="Edit Category">';
	}

	if ($User_Rights->authorized('deletecat')) {
		echo '<input type="button" class="dButton" onClick="CategoryManage.deleteCat();" value="Delete Category">';
	}
	?>
</form>

<?php echo View::loadJS("jquery", "catManage");?>
<script type="text/javascript">
    CategoryManage.grabNextLevel('0:0');
</script>
<!-- End Page Body -->

<?php include $paths['app'].'/pages/includes/footer.php'; ?>
