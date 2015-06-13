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
