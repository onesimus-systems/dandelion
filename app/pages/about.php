<?php
/**
 * Dandelion About page
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date March, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}

$requiredCssFiles = array("tutorial");
include ROOT.'/pages/includes/head.php';
?>
<!-- Begin Page Body -->
<div id="content">
	<h2 class="t_cen">About</h2>

	<p class="le" style="text-indent:0;">
		Version: <?php echo D_VERSION;?>
	</p>

    <h3>What is Dandelion:</h3>
    <p class="le">
    	Dandelion is designed to be a manual logging platform that can be used in a wide variety of environments. It was inspired and conceived out of an IT environment with the need to log changes to infrastructure. Dandelion provides a clean, easy to use interface for creating log entries so you remember what you did last Tuesday.
    </p>

    <h3>Creator:</h3>
    <p class="le">
    	Lee Keitel, Onesimus Computer Systems
    </p>

	<h3>Copyright:</h3>
    <p class="le" style="text-indent:0;">
    	Dandelion - Web-based entry log journal.<br>
		Copyright (C) 2014  Lee Keitel, Onesimus Computer Systems<br><br>

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
<?php include ROOT.'/pages/includes/footer.php'; ?>
