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
	<h2>An Error Has Occured</h2>

    <h3>Whoops! There appears to have been an error!</h3>

    <p class="noindent">
        <?= $this->e($message) ?>
    </p>
</div>
<!-- End Page Body -->
