<?php
/**
 * View utilities
 */
namespace Dandelion\Utils;

class view
{
    public static function loadJS()
    {
        $scripts = func_get_args();
        $scriptList = '';

        foreach ($scripts as $file) {
            // Check for a keyworded include
            $builtin = self::isBuiltinJsFile($file);
            if ($builtin) {
                $scriptList .= $builtin;
                continue;
            }

            // Otherwise check for a custom file
            $custom = self::isCustomJsFile($file);
            if ($custom) {
                $scriptList .= $custom;
                continue;
            }
        }
        return $scriptList;
    }

    private static function isBuiltinJsFile($name)
    {
        $include = '';

        switch (strtolower($name)) {
            case 'jquery':
                $include .= '<script src="/assets/js/vendor/jquery/js/jquery-2.1.1.min.js"></script>';
                break;
            case 'jqueryui':
                $include .= '<script src="/assets/js/vendor/jquery/js/jquery-ui-1.10.4.min.js"></script>';
                break;
            case 'tinymce':
                $include .= '<script src="/assets/js/vendor/tinymce/js/jquery.tinymce.min.js"></script>';
                $include .= '<script src="/assets/js/vendor/tinymce/js/tinymce.min.js"></script>';
                break;
            case 'cheesto':
                $include .= '<script src="/assets/js/presence.js"></script>';
                break;
        }
        return $include;
    }

    private static function isCustomJsFile($name)
    {
        // Normalize name
        if (substr($name, -3) != '.js') {
            $name .= '.js';
        }
        $include = '';

        if (is_file('assets/js/'.$name)) {
            $include .= '<script src="/assets/js/'.$name.'"></script>';
        } elseif (is_file('assets/js/vendor/jquery/js/'.$name)) {
            $include .= '<script src="/assets/js/vendor/jquery/js/'.$name.'"></script>';
        } else {
            $include .= "<!-- {$name} was not found. Error 404. -->";
        }
        return $include;
    }

    /**
     * Determine the theme to use. Either a user assigned theme or the default.
     *
     * @return string - Theme name
     */
    public static function getTheme()
    {
        // Check if a theme is given from the user settings session variable
        $theme = !empty($_SESSION['userInfo']['theme']) ? $_SESSION['userInfo']['theme'] : DEFAULT_THEME;
        // Next see if that theme is available
        $theme = is_dir(PUBLIC_DIR.'/'.THEME_DIR.'/'.$theme) ? $theme : DEFAULT_THEME;
        return $theme;
    }

    /**
     * Generate a list of available themes with the current used theme selected
     *
     * @param string $theme - Default null - Name of theme to have preselected
     * @return string - Generated HTML
     */
    public static function getThemeList($theme = null, $showDefaultOption = true)
    {
        /*
         * The call can pass the theme currently used,
         * if one isn't passed, assume the current user's theme
         */
        $currentTheme = ($theme===null) ? self::getTheme() : $theme;
        $currentTheme = ($theme==='') ? DEFAULT_THEME : $currentTheme;
        $themeList = '';

        if (!$handle = opendir(PUBLIC_DIR.'/'.THEME_DIR)) {
            return '';
        }

        $themeList .= '<select name="userTheme" id="userTheme">';
        if ($showDefaultOption) {
            $themeList .= '<option value="default">Default</option>';
        }
        while (false !== ($themeName = readdir($handle))) {
            if ($themeName != '.' && $themeName != '..' && is_dir(THEME_DIR.'/'.$themeName)) {
                $selected = ($themeName == $currentTheme) ? 'selected' : '';

                $themeList .= "<option value=\"{$themeName}\" {$selected}>{$themeName}</option>";
            }
        }
        $themeList .= '</select>';

        return $themeList;
    }

    public static function getThemeListArray()
    {
        $themeList = [];
        if (!$handle = opendir(PUBLIC_DIR.'/'.THEME_DIR)) {
            return '';
        }
        while (false !== ($themeName = readdir($handle))) {
            if ($themeName != '.' && $themeName != '..' && is_dir(THEME_DIR.'/'.$themeName)) {
                array_push($themeList, $themeName);
            }
        }
        return $themeList;
    }

    /**
     * Generates HTML link tags for given CSS files
     * Names are given as function arguments which are retrieved
     * by func_get_args()
     *
     * @return string - HTML link tags
     */
    public static function loadCssSheets()
    {
        $optionalSheets = func_get_args();
        $theme = self::getTheme();
        $cssList = '';
        $main = true;

        // Base/main CSS
        if (count($optionalSheets) > 0) {
            if ($optionalSheets[count($optionalSheets)-1] === false) {
                $main = false;
            }
        }

        if ($main) {
            $cssList .= '<link rel="stylesheet" type="text/css" href="/assets/styles/css/main.css">';
            $cssList .= '<link rel="stylesheet" type="text/css" href="/'.THEME_DIR.'/'.$theme.'/main.css">';
        }

        // Other stylesheets
        foreach ($optionalSheets as $sheet) {
            // Load manual filenames if given
            if (substr($sheet, -4) == '.css') {
                if (is_file('assets/styles/css/'.$sheet))
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/assets/styles/css/'.$sheet.'">';
                continue;
            }

            $sheet = strtolower($sheet);

            // Load keyworded stylesheets
            switch($sheet) {
                // CSS for Cheesto presence system
                case "cheesto":
                    // no break
                case 'presence':
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/assets/styles/css/presence.css">';
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/'.THEME_DIR.'/'.$theme.'/cheesto.css">';
                    break;

                // CSS for Cheesto presence system (windowed)
                case "cheestowin":
                    // no break
                case "presencewin":
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/assets/styles/css/presenceWin.css">';
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/'.THEME_DIR.'/'.$theme.'/presenceWin.css">';
                    break;

                // CSS for jQueryUI
                case "jquery":
                    // no break
                case "jqueryui":
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/assets/js/vendor/jquery/css/smoothness/jquery-ui-1.10.4.custom.min.css">';
                    break;

                // CSS for Tutorial
                case "tutorial":
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/assets/styles/css/tutorial.css">';
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/'.THEME_DIR.'/'.$theme.'/tutorial.css">';
                    break;

                // CSS for MailBox
                case "mail":
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/assets/styles/css/mail.css">';
                    $cssList .= '<link rel="stylesheet" type="text/css" href="/'.THEME_DIR.'/'.$theme.'/mail.css">';
                    break;
            }
        }

        return $cssList;
    }

    public static function redirect($page)
    {
        $allPages = array(
            'home' => '',
            'homepage' => '',
            'index' => '',
            'dashboard' => '',
            'userSettings' => 'settings',
            'adminSettings' => 'admin',
            'tutorial' => 'tutorial',
            'logout' => 'logout',
            'login' => 'login',
            'about' => 'about',
            'adminCategories' => 'categories',
            'adminGroups' => 'editgroups',
            'adminUsers' => 'editusers',
            'installer' => 'install/index.php',
            'mailbox' => 'mail',
            'resetPassword' => 'reset'
        );

        if (!array_key_exists($page, $allPages)) {
            trigger_error($page . ' is not an available redirect page.');
            return;
        }

        $newPath = '/' . $allPages[$page];
        header("Location: $newPath");
        return;
    }
}
