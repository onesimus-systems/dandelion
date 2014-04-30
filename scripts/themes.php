<?php
/**
  * Themes functions
  *
  * This file is a part of Dandelion
  * 
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

function getTheme() {
	$theme = !empty($_SESSION['userInfo']['theme']) ? $_SESSION['userInfo']['theme'] : 'default';
	$theme = is_dir(ROOT.'/'.THEME_DIR.'/'.$theme) ? $theme : 'default';
	return $theme;
}

function getThemeList($theme = null) {
	// The call can pass the theme currently used,
	// if one isn't passed, assume the current user's theme
	$currentTheme = ($theme===null) ? getTheme() : $theme;
	$handle = opendir('themes');
	
	echo '<select name="userTheme">';
	while (false !== ($themeName = readdir($handle))) {
		if ($themeName != '.' && $themeName != '..' && is_dir(THEME_DIR.'/'.$themeName)) {
			if ($themeName == $currentTheme) {
				echo '<option value="'.$themeName.'" selected>'.$themeName.'</option>';
			}
			else {
				echo '<option value="'.$themeName.'">'.$themeName.'</option>';
			}
		}
	}
	echo '</select>';
}

function printCssSheets($optional = array(), $mainBool = true) {
	$optionalSheets = array();
	$theme = getTheme();
	foreach ($optional as $temp) {
		$optionalSheets[] = strtolower($temp);
	}
	
	// Base/main CSS
	if ($mainBool) {
		echo '<link rel="stylesheet" type="text/css" href="styles/main.css">';
		echo '<link rel="stylesheet" type="text/css" href="themes/'.$theme.'/main.css">';
	}
	
	// CSS for Cheesto presence system
	if (in_array("cheesto", $optionalSheets)) {
		echo '<link rel="stylesheet" type="text/css" href="styles/presence.css">';
		echo '<link rel="stylesheet" type="text/css" href="themes/'.$theme.'/cheesto.css">';
	}
	
	// CSS for Cheesto presence system (windowed)
	if (in_array("cheestowin", $optionalSheets)) {
		echo '<link rel="stylesheet" type="text/css" href="styles/presenceWin.css">';
		echo '<link rel="stylesheet" type="text/css" href="themes/'.$theme.'/presenceWin.css">';
	}
	
	// CSS for jQueryUI
	if (in_array("jquery", $optionalSheets)) {
		echo '<link rel="stylesheet" type="text/css" href="jquery/css/smoothness/jquery-ui-1.10.4.custom.min.css">';
	}
	
	// CSS for Tutorial
	if (in_array("tutorial", $optionalSheets)) {
        echo '<link rel="stylesheet" type="text/css" href="styles/tutorial.css">';
		echo '<link rel="stylesheet" type="text/css" href="themes/'.$theme.'/tutorial.css">';
	}
	
	// Any manual CSS
	foreach ($optionalSheets as $manualSheet) {
		if (substr($manualSheet, -4) == ".css") {
			echo '<link rel="stylesheet" type="text/css" href="styles/'.$manualSheet.'">';
		}
	}
}