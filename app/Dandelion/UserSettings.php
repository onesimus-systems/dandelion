<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
  */
namespace Dandelion;

use Dandelion\Utils;
use Dandelion\Repos\Interfaces\UserSettingsRepo;

class UserSettings
{
    public function __construct(UserSettingsRepo $repo)
    {
        $this->repo = $repo;
    }

    public function saveLogLimit($limit = 25, $user = null)
    {
        if ($limit < 5) {
          $limit = 5;
        } elseif ($limit > 500) {
          $limit = 500;
        }

        $user = $user ?: $_SESSION['userInfo']['id'];

        if ($this->repo->saveLogViewLimit($user, $limit)) {
            $_SESSION['userInfo']['showlimit'] = $limit;
            return true;
        }
        return false;
    }

    public function saveTheme($theme = '', $user = null)
    {
        $user = $user ?: $_SESSION['userInfo']['id'];

        if ($this->repo->saveUserTheme($user, $theme)) {
            $_SESSION['userInfo']['theme'] = $theme;
            Utils\View::setThemeCookie($theme);
            return true;
        }
        return false;
    }

    public function getSetting($setting, $id)
    {
        return $this->repo->getUserSetting($id, $setting);
    }
}
