<?php
function getTheme() {
	// If a theme is defined for the user in the database, grab it
	$theme = !empty($_SESSION['userInfo']['theme']) ? $_SESSION['userInfo']['theme'] : 'default';
	
	// Check to see if the theme exists, if it doesn't fallback to default
	$theme = is_dir(THEME_DIR.'/'.$theme) ? $theme : 'default';
	return $theme;
}

function getThemeList($theme = null) {
	// The call can pass the theme to use as selected,
	// if one isn't passed, assume the current user's theme
	$theme = ($theme===null ? getTheme() : $theme);
	$handle = opendir('themes');
	
	echo '<select name="userTheme">';
	while (false !== ($themeName = readdir($handle))) {
		if ($themeName != '.' && $themeName != '..' && is_dir(THEME_DIR.'/'.$themeName)) {
			if ($themeName == $theme) {
				echo '<option value="'.$themeName.'" selected>'.$themeName.'</option>';
			}
			else {
				echo '<option value="'.$themeName.'">'.$themeName.'</option>';
			}
		}
	}
	echo '</select>';
}
