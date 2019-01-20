<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
$this->layout('layouts::main', ['css' => ['singlelog']]);
?>
<!-- Begin Page Body -->
<div id="content">
    <div id="log-data">
        <h2>Log #<?= $this->e($id) ?> - <?= $this->e($title) ?></h2> <?= $editButton ?>

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
            <span class="log-body"><?= $body ?></span>
        </p>
    </div>

    <form id="add-comment-form">
        <strong>Comment:</strong> <span id="error-message"></span><br>
        <textarea cols="50" rows="6" id="newComment"></textarea><br>
        <input type="hidden" id="logid" value="<?= $this->e($id) ?>">
        <button>Save Comment</button>
        <button type="button" id="cancel-new-btn">Cancel</button>
    </form>

    <hr>

    <section id="comment-section">
        <p>
            <strong>Comments:</strong> <?= $addCommentButton ?> <?= $newOldLink ?>
        </p>
        <div id="comments">
            <?php if (!$comments): ?>
            No comments
            <?php else: foreach ($comments as $comment): ?>
                <div class="comment" id="comment-<?= $comment['id'] ?>">
                    <span class="comment-author"><?= $comment['fullname'] ?></span>
                    <span class="comment-datetime"><?= $comment['created'] ?></span>
                    <span class="comment-body"><?= $comment['comment'] ?></span>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </section>
</div>

<?= $this->loadJS(['jquery', 'singlelog']); ?>
<!-- End Page Body -->
