<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['editlog']]);
?>
<!-- Begin Page Body -->
<div id="content">
    <div id="log-data">
        <h2>Create Log</h2>
        <input type="hidden" id="category-json" value="<?= $this->e($category) ?>">
        <form method="POST" action="save" id="edit-form">
            <?php if ($last_error !== ''): ?>
                <p>
                    <strong>Error:</strong> <?= $this->e($last_error) ?>
                </p>
            <?php endif; ?>
            <p>
                <strong>Title:</strong><br>
                <input type="text" name="title" value="<?= $this->e($title) ?>" size="60"></input>
            </p>
            <p>
                <span id="loading">Loading log textarea...</span>
                <textarea name="body" cols="60" rows="10" style="display: none;"><?= $body ?></textarea>
            </p>
            <p>
                <strong>Category:</strong> <span id="categories"></span>
            </p>

            <button type="submit">Save</button>
        </form>
    </div>
</div>

<?= $this->loadJS(['jquery', 'ckeditor', 'editlog']); ?>
<!-- End Page Body -->
