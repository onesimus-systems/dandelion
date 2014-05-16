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
namespace Dandelion;

function getTheme()
{
    // Check if a theme is given from the user settings session variable
    $theme = !empty($_SESSION['userInfo']['theme']) ? $_SESSION['userInfo']['theme'] : $_SESSION['app_settings']['default_theme'];
    // Next see if that theme is available
    $theme = is_dir(ROOT.'/'.THEME_DIR.'/'.$theme) ? $theme : $_SESSION['app_settings']['default_theme'];
    return $theme;
}

function getThemeList($theme = null)
{
    // The call can pass the theme currently used,
    // if one isn't passed, assume the current user's theme
    $currentTheme = ($theme===null) ? getTheme() : $theme;
    $currentTheme = ($theme=='default') ? $_SESSION['app_settings']['default_theme'] : $currentTheme;
    $themeList = '';

    $handle = opendir('themes');

    $themeList .= '<select name="userTheme" id="userTheme">';
    $themeList .= '<option value="default">Default</option>';
    while (false !== ($themeName = readdir($handle))) {
        if ($themeName != '.' && $themeName != '..' && is_dir(THEME_DIR.'/'.$themeName)) {
            $selected = ($themeName == $currentTheme) ? 'selected' : '';

            $themeList .= "<option value=\"{$themeName}\" {$selected}>{$themeName}</option>";
        }
    }
    $themeList .= '</select>';

    return $themeList;
}

function loadCssSheets()
{
    $optionalSheets = func_get_args();
    $theme = getTheme();
    $cssList = '';
    $main = true;

    // Base/main CSS
    if (count($optionalSheets) > 0) {
        if ($optionalSheets[count($optionalSheets)-1] === false) {
            $main = false;
        }
    }

    if ($main) {
        $cssList .= '<link rel="stylesheet" type="text/css" href="styles/main.css">';
        $cssList .= '<link rel="stylesheet" type="text/css" href="'.HOSTNAME.'/'.THEME_DIR.'/'.$theme.'/main.css">';
    }

    // Other stylesheets
    foreach ($optionalSheets as $sheet) {
        // Load manual filenames if given
        if (substr($sheet, -4) == '.css') {
            if (is_file('styles/'.$sheet))
                $cssList .= '<link rel="stylesheet" type="text/css" href="styles/'.$sheet.'">';
            continue;
        }

        $sheet = strtolower($sheet);

        // Load keyworded stylesheets
        switch($sheet) {
            // CSS for Cheesto presence system
            case "cheesto":
            case 'presence':
                $cssList .= '<link rel="stylesheet" type="text/css" href="styles/presence.css">';
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.HOSTNAME.'/'.THEME_DIR.'/'.$theme.'/cheesto.css">';
                break;

            // CSS for Cheesto presence system (windowed)
            case "cheestowin":
            case "presencewin":
                $cssList .= '<link rel="stylesheet" type="text/css" href="styles/presenceWin.css">';
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.HOSTNAME.'/'.THEME_DIR.'/'.$theme.'/presenceWin.css">';
                break;

            // CSS for jQueryUI
            case "jquery":
            case "jqueryui":
                $cssList .= '<link rel="stylesheet" type="text/css" href="jquery/css/smoothness/jquery-ui-1.10.4.custom.min.css">';
                break;

            // CSS for Tutorial
            case "tutorial":
                $cssList .= '<link rel="stylesheet" type="text/css" href="styles/tutorial.css">';
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.HOSTNAME.'/'.THEME_DIR.'/'.$theme.'/tutorial.css">';
                break;
        }
    }

    return $cssList;
}
