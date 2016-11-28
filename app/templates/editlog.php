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
        <h2>Edit Log</h2>
        <input type="hidden" id="category-json" value="<?= $this->e($category) ?>">
        <form method="POST" action="../save" id="edit-form">
            <input type="hidden" name="log-id" value="<?= $this->e($id) ?>">
            <?php if ($last_error !== ''): ?>
                <p>
                    <strong>Error:</strong> <?= $this->e($last_error) ?>
                </p>
            <?php endif; ?>
            <p>
                <strong>Title:</strong> <input type="text" name="title" value="<?= $this->e($title) ?>" size="60"></input>
            </p>
            <p>
                <strong>Author:</strong> <?= $this->e($author) ?>
            </p>
            <p>
                <strong>Created:</strong> <?= $this->e($date_created) ?> <?= $this->e($time_created) ?>
            </p>
            <p>
                <strong>Category:</strong> <span id="categories"></span>
            </p>
            <p>
                <span id="loading">Loading log textarea...</span>
                <textarea name="body" cols="60" rows="10" style="display: none;"><?= $body ?></textarea>
            </p>

            <button type="submit">Save</button>
        </form>
    </div>
</div>

<?= $this->loadJS(['jquery', 'common', 'categories', 'editlog', 'ckeditor']); ?>
<!-- End Page Body -->
