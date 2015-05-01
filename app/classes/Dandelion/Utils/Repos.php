<?php
/**
 * Utility to create repositories given the database type and module
 */

namespace Dandelion\Utils;

class Repos
{
    public static function makeRepo($module, $dbtype = null)
    {
        // Database type
        if (!$dbtype) {
            $dbtype = Configuration::getConfig()['db']['type'];
        }
        $dbtype = ucfirst($dbtype);
        $module = ucfirst($module);
        $repo = "\\Dandelion\\Repos\\{$dbtype}\\{$module}Repo";

        if (class_exists($repo)) {
            return new $repo();
        } else {
            return null;
        }
    }
}
