<?php
/**
  * This file handles javascript management in Dandelion
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date April 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

/**
 * Generates HTML script tags for given JS files
 * Names are given as function arguments which are retrieved
 * by func_get_args()
 *
 * @return string - HTML script tags
 */
function loadJS()
{
    $scripts = func_get_args();
    $scriptList = '';

    foreach($scripts as $file) {
        // Check for a keyworded include
        $builtin = isBuiltinJsFile($file);
        if ($builtin) {
            $scriptList .= $builtin;
            continue;
        }

        // Otherwise check for a custom file
        $custom = isCustomJsFile($file);
        if ($custom) {
            $scriptList .= $custom;
            continue;
        }
    }
    return $scriptList;
}

function isBuiltinJsFile($name) {
    global $User_Rights;
    $include = '';

    switch(strtolower($name)) {
        case 'jquery':
            $include .= '<script src="static/js/vendor/jquery/js/jquery-2.1.1.min.js"></script>';
            break;
        case 'jqueryui':
            $include .= '<script src="static/js/vendor/jquery/js/jquery-ui-1.10.4.min.js"></script>';
            break;
        case 'tinymce':
            $include .= '<script src="static/js/vendor/tinymce/js/jquery.tinymce.min.js"></script>';
            $include .= '<script src="static/js/vendor/tinymce/js/tinymce.min.js"></script>';
            break;
        case 'cheesto':
            if ($User_Rights->authorized('viewcheesto')) {
                $include .= '<script src="static/js/presence.js"></script>';
            }
            break;
    }
    return $include;
}

function isCustomJsFile($name) {
    // Normalize name
    if (substr($name, -3) != '.js') {
        $name .= '.js';
    }
    $include = '';

    if (is_file('static/js/'.$name)) {
        $include .= '<script src="static/js/'.$name.'"></script>';
    } elseif (is_file('static/js/vendor/jquery/js/'.$name)) {
        $include .= '<script src="static/js/vendor/jquery/js/'.$name.'"></script>';
    } else {
        $include .= "<!-- {$name} was not found. Error 404. -->";
    }
    return $include;
}