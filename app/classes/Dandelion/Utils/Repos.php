<?php
/**
 * Utility to create repositories given the database type and module
 */

namespace Dandelion\Utils;

class Repos
{
    public static function makeRepo($module)
    {
        $module = ucfirst($module);
        $repo = "\\Dandelion\\Repos\\{$module}Repo";

        if (class_exists($repo)) {
            return new $repo();
        } else {
            return null;
        }
    }
}
