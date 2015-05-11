<?php
/**
 * View utilities
 */
namespace Dandelion\Utils;

use Dandelion\Application;

class View
{
    private static $themeMetadataSchema = [
        'slug' => '',
        'name' => '',
        'author' => '',
        'email' => '',
        'description' => '',
        'version' => '',
        'files' => [],
        'extends' => ''
    ];

    private static $themeHttpDir = 'assets/themes';

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
     * @return string - Theme slug
     */
    public static function getTheme()
    {
        $config = Configuration::getConfig();
        $paths = Application::getPaths();

        if (isset($_COOKIE[$config['cookiePrefix'].'usertheme'])) {
            if (self::isTheme($paths['public'], $_COOKIE[$config['cookiePrefix'].'usertheme'])) {
                return $_COOKIE[$config['cookiePrefix'].'usertheme'];
            }
        } elseif (isset($_SESSION['userInfo']['theme'])) {
            if (self::isTheme($paths['public'], $_SESSION['userInfo']['theme'])) {
                self::setThemeCookie($_SESSION['userInfo']['theme']);
                return $_SESSION['userInfo']['theme'];
            }
        }

        return $config['defaultTheme'];
    }

    /**
     * Eligibility of a theme is determined by the existance of a metadata.json file
     *
     * @param $path string - Path to the public folder
     * @param $slug string - Theme slug to check
     *
     * @return boolean
     */
    private static function isTheme($path, $slug)
    {
        return is_file($path.'/assets/themes/'.$slug.'/metadata.json');
    }

    /**
     * Utility function to set the browser cookie for a theme
     *
     * @param $theme string - The theme slug to set
     *
     * @return void
     */
    public static function setThemeCookie($theme)
    {
        $config = Configuration::getConfig();
        setcookie($config['cookiePrefix'].'usertheme', $theme, time() + 60 * 60 * 24 * 30, '/');
        return;
    }

    /**
     * Generates and returns an array of valid theme metadata
     *
     * @return array
     */
    public static function getThemeListArray()
    {
        $paths = Application::getPaths();
        $themeList = [];
        $themeDir = $paths['public'].'/'.self::$themeHttpDir;
        $currentTheme = self::getTheme();

        if (!$handle = opendir($themeDir)) {
            return [];
        }

        while (false !== ($themeName = readdir($handle))) {
            $themeFiles = $themeDir.'/'.$themeName;
            if ($themeName != '.' && $themeName != '..' && is_dir($themeFiles)) {
                if (!self::isTheme($paths['public'], $themeName)) {
                    continue;
                }

                $metadata = self::loadThemeMetadata($themeDir, $themeName);
                if ($metadata['slug']) {
                    $metadata['selected'] = ($metadata['slug'] === $currentTheme);
                    array_push($themeList, $metadata);
                }
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
        $themes = [self::getTheme()]; // Array in case the theme extends another theme
        $baseTheme = $themes[0];
        $cssList = '';
        $paths = Application::getPaths();
        $config = Configuration::getConfig();
        $themeDir = $paths['public'].'/'.self::$themeHttpDir;

        // Determine if the main stylesheet should be loaded
        if (count($optionalSheets) == 0 || $optionalSheets[count($optionalSheets)-1] !== false) {
            array_unshift($optionalSheets, 'main');
        }
        // Unshift normalize last so it's loaded first
        array_unshift($optionalSheets, 'normalize');

        // Load metadata for base theme
        $metadataJson = self::loadThemeMetadata($themeDir, $baseTheme);

        // Check if the base theme extends another theme
        if ($metadataJson['extends']) {
            array_unshift($themes, $metadataJson['extends']);
        }

        $addedSpecial = []; // Used to prevent double loading of special stylesheets
        foreach ($themes as $theme) {
            foreach ($optionalSheets as $sheet) {
                if (!$sheet) { // Possiblity one of the elements may be a bool false
                    continue;
                }

                if ($theme == $baseTheme) {
                    // Use previously parsed JSON for base theme
                    $metaJson = $metadataJson;
                } else {
                    // Load metadata for extended theme
                    $metaJson = self::loadThemeMetadata($themeDir, $theme);
                }

                // Remove css, min.css extensions
                $normalized = strtolower($sheet);
                $normalized = str_replace('.min.css', '', $normalized);
                $normalized = str_replace('.css', '', $normalized);

                // Special case for jQueryUI and jHtmlArea styles
                if ($normalized == 'jqueryui' && !in_array('jqueryui', $addedSpecial)) {
                    $cssList .= '<link rel="stylesheet" type="text/css" href="'.$config['hostname'].'/assets/js/vendor/jquery/css/jquery-ui.min.css">';
                    array_push($addedSpecial, 'jqueryui');
                    continue;
                } elseif ($normalized == 'jhtmlarea' && !in_array('jhtmlarea', $addedSpecial)) {
                    $cssList .= '<link rel="stylesheet" type="text/css" href="'.$config['hostname'].'/assets/js/vendor/jhtmlarea/styles/jHtmlArea.css">';
                    array_push($addedSpecial, 'jhtmlarea');
                    continue;
                }

                // If the theme contains a map to a file for this style, use it
                if (array_key_exists($normalized, $metaJson['files'])) {
                    $cssList .= '<link rel="stylesheet" type="text/css" href="'.$config['hostname'].'/' . self::$themeHttpDir . '/' . $theme . '/' . $metaJson['files'][$normalized] . '">';
                } else {
                    // Otherwise search
                    if (is_file($themeDir . '/' . $theme . '/' . $normalized . '.min.css')) {
                        $cssList .= '<link rel="stylesheet" type="text/css" href="'.$config['hostname'].'/' . self::$themeHttpDir . '/' . $theme . '/' . $normalized . '.min.css">';
                    } elseif (is_file($themeDir . '/' . $theme . '/' . $normalized . '.css')) {
                        $cssList .= '<link rel="stylesheet" type="text/css" href="'.$config['hostname'].'/' . self::$themeHttpDir . '/' . $theme . '/' . $normalized . '.css">';
                    }
                }
            }
        }

        return $cssList;
    }

    /**
     * Opens and parses the metadata file for a theme
     *
     * @param $themeDir string - Public theme directory
     * @param $theme string - Theme slug to process
     *
     * @return array - Theme metadata formatted as $themeMetadataSchema
     */
    private static function loadThemeMetadata($themeDir, $theme)
    {
        $metadataJson = file_get_contents($themeDir.'/'.$theme.'/metadata.json');
        $metadataJson = json_decode($metadataJson, true);
        $metadataJson = array_merge(self::$themeMetadataSchema, $metadataJson);
        return $metadataJson;
    }

    /**
     * Issues a browser redirect to a specific page
     *
     * @param $page - Page slug to redirect
     *
     * @return void
     */
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
