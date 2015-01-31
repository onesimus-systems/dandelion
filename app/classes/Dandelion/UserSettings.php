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

    public function saveLogLimit($limit)
    {
        if ($limit < 5) {
          $limit = 5;
        } elseif ($limit > 500) {
          $limit = 500;
        }

        $this->repo->saveLogViewLimit($_SESSION['userInfo']['userid'], $limit);

        $_SESSION['userInfo']['showlimit'] = $limit;

        return 'Show limit changed successfully';
    }

    public function saveTheme($theme)
    {
        $theme = isset($theme) ? $theme : 'default';

        $this->repo->saveUserTheme($_SESSION['userInfo']['userid'], $theme);

        $_SESSION['userInfo']['theme'] = $theme;

        return 'Theme saved successfully';
    }

    public function getSetting($setting, $id)
    {
        return $this->repo->getUserSetting($id, $setting);
    }
}
