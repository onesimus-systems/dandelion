<?php
/**
 * Category management page
 */
$this->layout('layouts::main', ['requiredCssFiles' => []]);
?>
<!-- Begin Page Body -->
<h3>Category Management</h3>

<div id="message"></div>

<form method="post">
	<div name="categorySelects" id="categorySelects"></div><br><br>

	<?php
	if ($userRights->authorized('addcat')) {
		echo '<input type="button" class="dButton" onClick="CategoryManage.createNew();" value="Add Category">';
	}

	if ($userRights->authorized('editcat')) {
		echo '<input type="button" class="dButton" onClick="CategoryManage.editCat();" value="Edit Category">';
	}

	if ($userRights->authorized('deletecat')) {
		echo '<input type="button" class="dButton" onClick="CategoryManage.deleteCat();" value="Delete Category">';
	}
	?>
</form>

<?= $this->loadJS(['jquery','catManage']) ?>
<script type="text/javascript">
    CategoryManage.grabNextLevel('0:0');
</script>
<!-- End Page Body -->
