<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['requiredCssFiles' => []]);
?>
<!-- Begin Page Body -->
<div id="content">
	<h2>Log #<?= $this->e($id) ?> - <?= $this->e($title) ?></h2>
	<?= $editButton ?>

    <p>
        <strong>Author:</strong> <?= $this->e($author) ?>
    </p>
	<p>
        <strong>Created:</strong> <?= $this->e($date_created) ?> <?= $this->e($time_created) ?>
    </p>
	<p>
		<strong>Category:</strong> <?= $this->e($category) ?>
	</p>
	<p>
        <strong>Edited:</strong> <?= $this->e($is_edited) ?>
    </p>

	<p>
		<strong>Log Contents:</strong>
	</p>
	<p>
		<?= $this->e($body) ?>
	</p>
</div>
<!-- End Page Body -->
