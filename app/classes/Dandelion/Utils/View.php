<?php
/**
 * View utilities
 */
namespace Dandelion\Utils;

use Dandelion\Application;

class view
{
    public static function loadJS()
    {
        $scripts = func_get_args();
        $scriptList = '';
        $config = Configuration::getConfig();

        foreach ($scripts as $file) {
            // Check for a keyworded include
            $builtin = self::isVenderJS($file, $config['hostname']);
            if ($builtin) {
                $scriptList .= $builtin;
                continue;
            }

            // Otherwise check for a custom file
            $custom = self::isJSFile($file, $config['hostname']);
            if ($custom) {
                $scriptList .= $custom;
                continue;
            }
        }
        return $scriptList;
    }

    private static function isVenderJS($name, $hostname)
    {
        $include = '';

        switch (strtolower($name)) {
            case 'jquery':
                $include .= '<script src="'.$hostname.'/assets/js/vendor/jquery/js/jquery-2.1.3.min.js"></script>';
                break;
            case 'jqueryui':
                $include .= '<script src="'.$hostname.'/assets/js/vendor/jquery/js/jquery-ui-1.11.3.min.js"></script>';
                break;
            case 'jhtmlarea':
                $include .= '<script src="'.$hostname.'/assets/js/vendor/jhtmlarea/jHtmlArea-0.8.min.js"></script>';
                break;
        }
        return $include;
    }

    private static function isJSFile($name, $hostname)
    {
        $paths = Application::getPaths();

        // Normalize name
        if (substr($name, -7) != '.min.js') {
            $name .= '.min.js';
        }
        $include = '';

        if (is_file($paths['public'] . '/build/js/'.$name)) {
            $include .= '<script src="'.$hostname.'/build/js/'.$name.'"></script>';
        } elseif (is_file($paths['public'] . '/assets/js/vendor/jquery/js/'.$name)) {
            $include .= '<script src="'.$hostname.'/assets/js/vendor/jquery/js/'.$name.'"></script>';
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

        $themeList .= '<select id="theme">';
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
        $paths = Application::getPaths();
        $config = Configuration::getConfig();

        $cssList .= self::findStyleSheet('normalize', $paths, $config['hostname']);
        if (count($optionalSheets) == 0 || $optionalSheets[count($optionalSheets)-1] !== false) {
            $cssList .= self::findStyleSheet('main', $paths, $config['hostname']);
            $cssList .= self::findThemeStyleSheet('main', $paths, $theme, $config['hostname']);
        }

        // Other stylesheets
        foreach ($optionalSheets as $sheet) {
            $normalized = strtolower($sheet);
            $normalized = str_replace('.min.css', '', $normalized);
            $normalized = str_replace('.css', '', $normalized);

            // Special case for jQueryUI styles
            if ($normalized == 'jqueryui') {
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.$config['hostname'].'/assets/js/vendor/jquery/css/jquery-ui.min.css">';
                continue;
            } elseif ($normalized == 'jhtmlarea') {
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.$config['hostname'].'/assets/js/vendor/jhtmlarea/styles/jHtmlArea.css">';
                continue;
            }

            $cssList .= self::findStyleSheet($normalized, $paths, $config['hostname']);
            $cssList .= self::findThemeStyleSheet($normalized, $paths, $theme, $config['hostname']);
        }

        return $cssList;
    }

    public static function findStyleSheet($name, $paths, $hostname)
    {
        if (is_file($paths['public'] . '/build/css/' . $name . '.min.css')) {
            return '<link rel="stylesheet" type="text/css" href="' . $hostname . '/build/css/' . $name . '.min.css">';
        } elseif (is_file($paths['public'] . '/build/css/' . $name . '.css')) {
            return '<link rel="stylesheet" type="text/css" href="' . $hostname . '/build/css/' . $name . '.css">';
        }
    }

    public static function findThemeStyleSheet($name, $paths, $theme, $hostname)
    {
        if (is_file($paths['themes'] . '/' . $theme . '/css/' . $name . '.min.css')) {
            return '<link rel="stylesheet" type="text/css" href="'.$hostname.'/' . THEME_DIR . '/' . $theme . '/css/' . $name . '.min.css">';
        } elseif (is_file($paths['themes'] . '/' . $theme . '/css/' . $name . '.css')) {
            return '<link rel="stylesheet" type="text/css" href="'.$hostname.'/' . THEME_DIR . '/' . $theme . '/css/' . $name . '.css">';
        }
    }

    public static function redirect($page)
    {
        $config = Configuration::getConfig();
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

        $newPath = $config['hostname'] . '/' . $allPages[$page];
        header("Location: $newPath");
        return;
    }
}
