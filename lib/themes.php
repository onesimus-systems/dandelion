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

/**
 * Determine the theme to use. Either a user assigned theme or the default.
 *
 * @return string - Theme name
 */
function getTheme()
{
    // Check if a theme is given from the user settings session variable
    $theme = !empty($_SESSION['userInfo']['theme']) ? $_SESSION['userInfo']['theme'] : $_SESSION['app_settings']['default_theme'];
    // Next see if that theme is available
    $theme = is_dir(ROOT.'/'.THEME_DIR.'/'.$theme) ? $theme : $_SESSION['app_settings']['default_theme'];
    return $theme;
}

/**
 * Generate a list of available themes with the current used theme selected
 *
 * @param string $theme - Default null - Name of theme to have preselected
 * @return string - Generated HTML
 */
function getThemeList($theme = null)
{
    /*
     * The call can pass the theme currently used,
     * if one isn't passed, assume the current user's theme
     */
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

/**
 * Generates HTML link tags for given CSS files
 * Names are given as function arguments which are retrieved
 * by func_get_args()
 *
 * @return string - HTML link tags
 */
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
        $cssList .= '<link rel="stylesheet" type="text/css" href="static/styles/main.css">';
        $cssList .= '<link rel="stylesheet" type="text/css" href="'.THEME_DIR.'/'.$theme.'/main.css">';
    }

    // Other stylesheets
    foreach ($optionalSheets as $sheet) {
        // Load manual filenames if given
        if (substr($sheet, -4) == '.css') {
            if (is_file('static/styles/'.$sheet))
                $cssList .= '<link rel="stylesheet" type="text/css" href="static/styles/'.$sheet.'">';
            continue;
        }

        $sheet = strtolower($sheet);

        // Load keyworded stylesheets
        switch($sheet) {
            // CSS for Cheesto presence system
            case "cheesto":
            case 'presence':
                $cssList .= '<link rel="stylesheet" type="text/css" href="static/styles/presence.css">';
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.THEME_DIR.'/'.$theme.'/cheesto.css">';
                break;

            // CSS for Cheesto presence system (windowed)
            case "cheestowin":
            case "presencewin":
                $cssList .= '<link rel="stylesheet" type="text/css" href="static/styles/presenceWin.css">';
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.THEME_DIR.'/'.$theme.'/presenceWin.css">';
                break;

            // CSS for jQueryUI
            case "jquery":
            case "jqueryui":
                $cssList .= '<link rel="stylesheet" type="text/css" href="static/js/jquery/css/smoothness/jquery-ui-1.10.4.custom.min.css">';
                break;

            // CSS for Tutorial
            case "tutorial":
                $cssList .= '<link rel="stylesheet" type="text/css" href="static/styles/tutorial.css">';
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.THEME_DIR.'/'.$theme.'/tutorial.css">';
                break;

            // CSS for MailBox
            case "mail":
                $cssList .= '<link rel="stylesheet" type="text/css" href="static/styles/mail.css">';
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.THEME_DIR.'/'.$theme.'/mail.css">';
                break;
        }
    }

    return $cssList;
}
