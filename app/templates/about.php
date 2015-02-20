<?php
/**
 * Dandelion About page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['generic']]);
?>
<!-- Begin Page Body -->
<div id="content">
	<h2>About</h2>

	<p class="noindent">
		Version: <?= $this->e($appVersion) ?>
	</p>

    <h3>What is Dandelion:</h3>
    <p>
    	Dandelion is designed to be a manual logging platform that can be used in a wide variety of environments. It was inspired and conceived out of an IT environment with the need to log changes to infrastructure. Dandelion provides a clean, easy to use interface for creating log entries so you remember what you did last Tuesday.
    </p>

    <h3>Creator:</h3>
    <p>
    	Lee Keitel, Onesimus Computer Systems &lt;keitellf@gmail.com&gt;
    </p>

    <h3>Source:</h3>
    <p>
        The source code for Dandelion is availabe on <a href="https://github.com/dragonrider23/dandelion" target="_blank">GitHub</a>.
    </p>

	<h3>Copyright:</h3>
    <p class="noindent">
    	Dandelion - Web-based entry log journal.<br>
		Copyright &copy; 2014  Lee Keitel, Onesimus Computer Systems<br><br>

		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License.<br><br>

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.<br><br>

		A copy of the license is available in LICENSE.md).
		You can also read the license online at <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_BLANK">https://www.gnu.org/licenses/gpl-3.0.html</a>.
    </p>
</div>
<!-- End Page Body -->
