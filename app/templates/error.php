<?php
/**
 * Dandelion generic error page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['tutorial']]);
?>
<!-- Begin Page Body -->
<div id="content">
	<h2 class="t_cen">An Error Has Occured</h2>

    <h3>Whoops! There appears to have been an error!</h3>

    <p>
        <?= $this->e($message) ?>
    </p>
</div>
<!-- End Page Body -->
