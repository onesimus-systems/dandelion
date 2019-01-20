<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Utils;

use Dandelion\Application;
use Dandelion\Utils\Configuration as Config;
use Dandelion\Exception\ShutdownException;
use Dandelion\Session\SessionManager as Session;

class View
{
    // Schema for a theme metadata file
    private static $themeMetadataSchema = [
        'slug' => '',
        'name' => 'Unnamed',
        'author' => '',
        'email' => '',
        'description' => '',
        'version' => '',
        'files' => [],
        'extends' => ''
    ];

    // Subdirectory path to themes as seen from the browser
    private static $themeHttpDir = 'assets/themes';

    // The limit of how long a theme extend chain can be
    private static $extendsLimit = 5;

    public static function loadJS()
    {
        $scripts = func_get_args();
        $scriptList = '';

        foreach ($scripts as $file) {
            // Check for a keyworded include
            $builtin = self::isVenderJS($file, Config::get('hostname', ''));
            if ($builtin) {
                $scriptList .= $builtin;
                continue;
            }

            // Otherwise check for a custom file
            $custom = self::isJSFile($file, Config::get('hostname', ''));
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
                $include = '<script src="'.$hostname.'/assets/vendor/jquery/js/jquery-3.3.1.min.js"></script>';
                break;
            case 'jqueryui':
                $include = '<script src="'.$hostname.'/assets/vendor/jquery/js/jquery-ui-1.12.1.min.js"></script>';
                break;
            case 'ckeditor':
                $include = '<script src="'.$hostname.'/assets/vendor/ckeditor/ckeditor.js"></script>';
                break;
        }
        return $include;
    }

    private static function isJSFile($name, $hostname)
    {
        $public = Application::getPaths()['public'];
        $choices = [$name.'.js', $name.'.min.js'];

        foreach ($choices as $script) {
            $filepath = $public . '/js/' . $script;
            $vendorFilepath = $public . '/assets/js/vendor/jquery/js/' . $script;

            if (is_file($filepath)) {
                $hash = md5_file($filepath);
                return '<script src="'.$hostname.'/js/'.$script.'?'.$hash.'"></script>';
            } elseif (is_file($vendorFilepath)) {
                $hash = md5_file($vendorFilepath);
                return '<script src="'.$hostname.'/assets/js/vendor/jquery/js/'.$script.'?'.$hash.'"></script>';
            }
        }
    }

    /**
     * Determine the theme to use. Either a user assigned theme or the default.
     *
     * @return string - Theme slug
     */
    public static function getTheme()
    {
        $paths = Application::getPaths();

        if (isset($_COOKIE[Config::get('cookiePrefix').'usertheme'])) {
            if (self::isTheme($_COOKIE[Config::get('cookiePrefix').'usertheme'])) {
                return $_COOKIE[Config::get('cookiePrefix').'usertheme'];
            }
        } elseif (isset(Session::get('userInfo')['theme'])) {
            if (self::isTheme(Session::get('userInfo')['theme'])) {
                self::setThemeCookie(Session::get('userInfo')['theme']);
                return Session::get('userInfo')['theme'];
            }
        }

        return Config::get('defaultTheme'); // Returns earlier if possible
    }

    /**
     * Eligibility of a theme is determined by the existance of a metadata.json file
     *
     * @param $slug string - Theme slug to check
     *
     * @return boolean
     */
    private static function isTheme($slug)
    {
        if (!$slug) {
            return false;
        }

        $paths = Application::getPaths();
        return is_file($paths['public'].'/assets/themes/'.$slug.'/metadata.json');
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
        setcookie(Config::get('cookiePrefix').'usertheme', $theme, time() + 60 * 60 * 24 * 30, '/');
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
                if (!self::isTheme($themeName)) {
                    continue;
                }

                $metadata = self::loadThemeMetadata($themeName);
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
        if (count($optionalSheets) === 0) {
            return '';
        }

        $baseTheme = self::getTheme();
        $cssList = '';
        $paths = Application::getPaths();
        $themeDir = $paths['public'].'/'.self::$themeHttpDir;

        // Determine if the main stylesheet should be loaded
        if (count($optionalSheets) == 0 || $optionalSheets[count($optionalSheets)-1] !== false) {
            array_unshift($optionalSheets, 'main');
        }
        // Unshift normalize last so it's loaded first
        array_unshift($optionalSheets, 'normalize');

        // Load theme extend chain
        $themes = self::getExtendChain(self::loadThemeMetadata($baseTheme));

        $addedSpecial = []; // Used to prevent double loading of special stylesheets
        foreach ($themes as $metaJson) {
            if (!array_key_exists('files', $metaJson)) {
                $metaJson['files'] = [];
            }

            foreach ($optionalSheets as $sheet) {
                if (!$sheet) { // Possiblity one of the elements may be a bool false
                    continue;
                }
                $theme = $metaJson['slug'];

                // Remove css, min.css extensions
                $normalized = strtolower($sheet);
                $normalized = str_replace('.min.css', '', $normalized);
                $normalized = str_replace('.css', '', $normalized);

                // Special case for jQueryUI and datetimepicker styles
                if ($normalized == 'jqueryui' && !in_array('jqueryui', $addedSpecial)) {
                    $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/assets/vendor/jquery/css/jquery-ui.min.css">';
                    $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/assets/vendor/jquery/css/jquery-ui-theme.min.css">';
                    array_push($addedSpecial, 'jqueryui');
                    continue;
                } elseif ($normalized == 'datetimepicker' && !in_array('datetimepicker', $addedSpecial)) {
                    $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/assets/vendor/jquery/css/datetimepicker.min.css">';
                    array_push($addedSpecial, 'datetimepicker');
                    continue;
                }

                // If the theme contains a map to a file for this style, use it
                if (array_key_exists($normalized, $metaJson['files']) &&
                    is_file($themeDir . '/' . $theme . '/' . $metaJson['files'][$normalized])) {
                    $hash = md5_file($themeDir . '/' . $theme . '/' . $metaJson['files'][$normalized]);
                    $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/' . self::$themeHttpDir . '/' . $theme . '/' . $metaJson['files'][$normalized] . '?'.$hash.'">';
                } else {
                    // Otherwise search
                    if (is_file($themeDir . '/' . $theme . '/' . $normalized . '.min.css')) {
                        $hash = md5_file($themeDir . '/' . $theme . '/' . $normalized . '.min.css');
                        $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/' . self::$themeHttpDir . '/' . $theme . '/' . $normalized . '.min.css?'.$hash.'">';
                    } elseif (is_file($themeDir . '/' . $theme . '/' . $normalized . '.css')) {
                        $hash = md5_file($themeDir . '/' . $theme . '/' . $normalized . '.css');
                        $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/' . self::$themeHttpDir . '/' . $theme . '/' . $normalized . '.css?'.$hash.'">';
                    }
                }
            }
        }

        return $cssList;
    }

    /**
     * Compile the chain of extended themes from the base theme
     *
     * @param $baseMeta array - Metadata for the base theme
     *
     * @return array - List of theme metadata in the order they should be loaded.
     */
    private static function getExtendChain($baseMeta)
    {
        $chain = [$baseMeta];
        $themes = [$baseMeta['slug']]; // Track loops in the extend chain

        for ($i = 0; $i < self::$extendsLimit; $i++) {
            if (!$chain[0]['extends']) {
                // The last theme in the chain doesn't extend anything
                break;
            }

            // Make sure the theme isn't already in the chain and is a theme
            if (!in_array($chain[0]['extends'], $themes) && self::isTheme($chain[0]['extends'])) {
                array_unshift($chain, self::loadThemeMetadata($chain[0]['extends']));
            } else {
                break;
            }
        }

        return $chain;
    }

    /**
     * Opens and parses the metadata file for a theme
     *
     * @param $themeDir string - Public theme directory
     * @param $theme string - Theme slug to process
     *
     * @return array - Theme metadata formatted as $themeMetadataSchema
     */
    private static function loadThemeMetadata($theme)
    {
        $paths = Application::getPaths();
        $themeDir = $paths['public'].'/'.self::$themeHttpDir;
        $metadataJson = file_get_contents($themeDir.'/'.$theme.'/metadata.json');
        $metadataJson = json_decode($metadataJson, true);
        $metadataJson = array_merge(self::$themeMetadataSchema, $metadataJson);
        $metadataJson['slug'] = $metadataJson['slug'] ?: $theme;
        return $metadataJson;
    }

    public static function loadSimpleCss()
    {
        $optionalSheets = func_get_args();
        if (count($optionalSheets) === 0) {
            return '';
        }

        $cssList = '';
        $paths = Application::getPaths();
        $stylesDir = $paths['public'].'/styles';

        $addedSpecial = []; // Used to prevent double loading of special stylesheets
        foreach ($optionalSheets as $sheet) {
            if (!$sheet) { // Possiblity one of the elements may be a bool false
                continue;
            }

            // Remove css, min.css extensions
            $normalized = strtolower($sheet);
            $normalized = str_replace('.min.css', '', $normalized);
            $normalized = str_replace('.css', '', $normalized);

            // Special case for jQueryUI and datetimepicker styles
            if ($normalized == 'jqueryui' && !in_array('jqueryui', $addedSpecial)) {
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/assets/vendor/jquery/css/jquery-ui.min.css">';
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/assets/vendor/jquery/css/jquery-ui-theme.min.css">';
                array_push($addedSpecial, 'jqueryui');
                continue;
            } elseif ($normalized == 'datetimepicker' && !in_array('datetimepicker', $addedSpecial)) {
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/assets/vendor/jquery/css/datetimepicker.min.css">';
                array_push($addedSpecial, 'datetimepicker');
                continue;
            }

            // Otherwise search
            if (is_file($stylesDir . '/' . $normalized . '.min.css')) {
                $hash = md5_file($stylesDir . '/' . $normalized . '.min.css');
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/styles/' . $normalized . '.min.css?'.$hash.'">';
            } elseif (is_file($stylesDir . '/' . $normalized . '.css')) {
                $hash = md5_file($stylesDir . '/' . $normalized . '.css');
                $cssList .= '<link rel="stylesheet" type="text/css" href="'.Config::get('hostname', '').'/styles/' . $normalized . '.css?'.$hash.'">';
            }
        }

        return $cssList;
    }

    /**
     * Issues a browser redirect to a specific page
     *
     * @param $page - Page slug to redirect
     *
     * @return void
     */
    public static function redirect($page, $urlParams = [])
    {
        $app = Application::getInstance();
        $allPages = [
            'home' => '',
            'homepage' => '',
            'index' => '',
            'dashboard' => '',
            'userSettings' => 'settings',
            'adminSettings' => 'admin',
            'logout' => 'logout',
            'login' => 'login',
            'about' => 'about',
            'installer' => 'install/index.php',
            'resetPassword' => 'reset',
        ];

        if (!array_key_exists($page, $allPages)) {
            trigger_error($page . ' is not an available redirect page.', E_USER_ERROR);
            return;
        }

        $params = "";

        foreach ($urlParams as $key => $value) {
            $params .= urlencode($key)."=".urlencode($value)."&";
        }

        $newPath = Config::get('hostname', '') . '/' . $allPages[$page];
        if (!empty($params)) {
            $newPath .= "?" . rtrim($params,"&");;
        }

        $app->response->redirect($newPath);
        throw new ShutdownException();
        return;
    }
}
