<?php
/**
 * User settings management API module
 */
namespace Dandelion\API\Module;

use \Dandelion\UserSettings;
use \Dandelion\Utils\View;

class UserSettingsAPI extends BaseModule
{
    public function saveLogLimit()
    {
        $settings = new UserSettings($this->repo);
        return $settings->saveLogLimit($this->up->limit);
    }

    public function saveTheme()
    {
        $settings = new UserSettings($this->repo);
        return $settings->saveTheme($this->up->theme);
    }

    public function getThemeList()
    {
        return View::getThemeListArray();
    }
}
