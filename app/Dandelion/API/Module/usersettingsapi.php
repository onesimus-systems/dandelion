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

use \Dandelion\UserSettings;
use \Dandelion\Utils\View;
use \Dandelion\Exception\ApiException;

class UserSettingsAPI extends BaseModule
{
    public function saveLogLimit()
    {
        $settings = new UserSettings($this->repo);

        if ($settings->saveLogLimit((int) $this->up->limit, USER_ID)) {
            return 'Setting saved';
        } else {
            throw new ApiException('Error saving setting', 5);
        }
    }

    public function saveTheme()
    {
        $settings = new UserSettings($this->repo);

        if ($settings->saveTheme($this->up->theme, USER_ID)) {
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
