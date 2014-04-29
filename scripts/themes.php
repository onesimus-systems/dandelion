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
