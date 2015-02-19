<?php
/**
 * Dandelion generic error page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['generic']]);
?>
<!-- Begin Page Body -->
<div id="content">
	<h2>An Error Has Occured</h2>

    <h3>Whoops! There appears to have been an error!</h3>

    <p class="noindent">
        <?= $this->e($message) ?>
    </p>
</div>
<!-- End Page Body -->
