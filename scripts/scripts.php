<?php
/**
  * This file handles javascript management in Dandelion
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date April 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/

function loadJS() {
	$scripts = func_get_args();

	foreach($scripts as $file) {
		// Check to see if it's a manually supplied JS file
		if (substr($file, -3) == ".js") {
			if (is_file('js/'.$file)) {
				echo '<script src="js/'.$file.'"></script>';
			}
			elseif (is_file('jquery/js/'.$file)) {
				echo '<script src="jquery/js/'.$file.'"></script>';
			}
			else {
				echo $file.' was not found. Error 404.';
			}
			continue;
		}
		
		$file = strtolower($file);
		
		switch($file) {
			case "jquery":
				echo '<script src="jquery/js/jquery-2.1.0.min.js"></script>';
				break;
			case "jqueryui":
				echo '<script src="jquery/js/jquery-ui-1.10.4.custom.min.js"></script>';
				break;
			case "lquery":
				echo '<script src="js/lQuery.js"></script>';
				break;
			case "catmanage":
				echo '<script src="js/catManage.js"></script>';
				break;
			case "main":
				echo '<script src="js/mainScripts.js"></script>';
				break;
			case "cheesto":
				echo '<script src="js/presence.js"></script>';
				break;
		}
	}
}