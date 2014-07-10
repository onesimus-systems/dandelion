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
        // Check to see if it's a manually supplied JS file
        if (substr($file, -3) == ".js") {
            if (is_file('js/'.$file)) {
                $scriptList .= '<script src="js/'.$file.'"></script>';
            } elseif (is_file('jquery/js/'.$file)) {
                $scriptList .= '<script src="jquery/js/'.$file.'"></script>';
            } else {
                $scriptList .= "<!-- {$file} was not found. Error 404. -->";
            }
            continue;
        }

        $file = strtolower($file);

        switch($file) {
            case "jquery":
                $scriptList .= '<script src="js/jquery/js/jquery-2.1.1.min.js"></script>';
                break;
            case "jqueryui":
                $scriptList .= '<script src="js/jquery/js/jquery-ui-1.10.4.min.js"></script>';
                break;
            case "tinymce":
                $scriptList .= '<script src="js/tinymce/js/jquery.tinymce.min.js"></script>';
                $scriptList .= '<script src="js/tinymce/js/tinymce.min.js"></script>';
                break;
            case "catmanage":
                $scriptList .= '<script src="js/catManage.js"></script>';
                break;
            case "main":
                $scriptList .= '<script src="js/mainScripts.js"></script>';
                break;
            case "cheesto":
            case 'presence':
                if ($_SESSION['rights']['viewcheesto']) {
                    $scriptList .= '<script src="js/presence.js"></script>';
                }
                break;
        }
    }

    return $scriptList;
}
