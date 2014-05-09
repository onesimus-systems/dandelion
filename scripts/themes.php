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
	$theme = !empty($_SESSION['userInfo']['theme']) ? $_SESSION['userInfo']['theme'] : $_SESSION['app_settings']['default_theme'];
	$theme = is_dir(ROOT.'/'.THEME_DIR.'/'.$theme) ? $theme : $_SESSION['app_settings']['default_theme'];
	return $theme;
}

function getThemeList($theme = null) {
	// The call can pass the theme currently used,
	// if one isn't passed, assume the current user's theme
	$currentTheme = ($theme===null) ? getTheme() : $theme;
	
	if ($theme == 'default') {
		$currentTheme = $_SESSION['app_settings']['default_theme'];
	}
	
	$handle = opendir('themes');
	
	echo '<select name="userTheme" id="userTheme">';
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

function loadCssSheets() {
	$optionalSheets = func_get_args();
	$theme = getTheme();
	
	// Base/main CSS
	if ($optionalSheets[count($optionalSheets)-1] !== false) {
		echo '<link rel="stylesheet" type="text/css" href="styles/main.css">';
		echo '<link rel="stylesheet" type="text/css" href="'.HOSTNAME.'/'.THEME_DIR.'/'.$theme.'/main.css">';
	}
	
	foreach ($optionalSheets as $sheet) {
		if (substr($sheet, -4) == '.css') {
			if (is_file('styles/'.$sheet))
				echo '<link rel="stylesheet" type="text/css" href="styles/'.$sheet.'">';
			continue;
		}
		
		$sheet = strtolower($sheet);

		switch($sheet) {
			// CSS for Cheesto presence system
			case "cheesto":
			case 'presence':
				echo '<link rel="stylesheet" type="text/css" href="styles/presence.css">';
				echo '<link rel="stylesheet" type="text/css" href="'.HOSTNAME.'/'.THEME_DIR.'/'.$theme.'/cheesto.css">';
				break;
			
			// CSS for Cheesto presence system (windowed)
			case "cheestowin":
				echo '<link rel="stylesheet" type="text/css" href="styles/presenceWin.css">';
				echo '<link rel="stylesheet" type="text/css" href="'.HOSTNAME.'/'.THEME_DIR.'/'.$theme.'/presenceWin.css">';
				break;
			
			// CSS for jQueryUI
			case "jquery":
				echo '<link rel="stylesheet" type="text/css" href="jquery/css/smoothness/jquery-ui-1.10.4.custom.min.css">';
				break;
			
			// CSS for Tutorial
			case "tutorial":
		        echo '<link rel="stylesheet" type="text/css" href="styles/tutorial.css">';
				echo '<link rel="stylesheet" type="text/css" href="'.HOSTNAME.'/'.THEME_DIR.'/'.$theme.'/tutorial.css">';
				break;
		}
	}
}