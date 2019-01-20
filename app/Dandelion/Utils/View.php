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
