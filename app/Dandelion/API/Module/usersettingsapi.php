<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\Module;

use Dandelion\UserSettings;
use Dandelion\Utils\View;
use Dandelion\Exception\ApiException;

class UserSettingsAPI extends BaseModule
{
    public function saveLogLimit()
    {
        return $this->saveSetting('LogLimit', $this->up->limit);
    }

    public function saveTheme()
    {
        return $this->saveSetting('Theme', $this->up->theme);
    }

    private function saveSetting($setting, $value)
    {
        $settings = new UserSettings($this->repo);
        $method = "save{$setting}";

        if ($settings->$method($value, USER_ID)) {
            return 'Setting saved';
        } else {
            throw new ApiException('Error saving setting', 5);
        }
    }

    public function getThemeList()
    {
        return View::getThemeListArray();
    }
}
