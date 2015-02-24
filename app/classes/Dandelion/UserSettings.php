<?php
/**
 * Manage user settings
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\UserSettingsRepo;

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

        $user = $user ?: $_SESSION['userInfo']['userid'];

        if ($this->repo->saveLogViewLimit($user, $limit)) {
            $_SESSION['userInfo']['showlimit'] = $limit;
            return 'Show limit changed successfully';
        } else {
            return 'An error occured saving your settings';
        }
    }

    public function saveTheme($theme = 'default', $user = null)
    {
        $user = $user ?: $_SESSION['userInfo']['userid'];

        if ($this->repo->saveUserTheme($user, $theme)) {
            $_SESSION['userInfo']['theme'] = $theme;
            return 'Theme saved successfully';
        } else {
            return 'An error occured saving your settings';
        }
    }

    public function getSetting($setting, $id)
    {
        return $this->repo->getUserSetting($id, $setting);
    }
}
