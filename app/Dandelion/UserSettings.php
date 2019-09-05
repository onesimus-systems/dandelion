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
use Dandelion\Session\SessionManager as Session;
use Dandelion\Repos\Interfaces\UserSettingsRepo;

class UserSettings
{
    protected $repo;

    public function __construct(UserSettingsRepo $repo)
    {
        $this->repo = $repo;
    }

    public function saveLogLimit($limit = 25, $user)
    {
        if (Session::get('userInfo')['logs_per_page'] == $limit) {
            return true;
        }

        if ($limit < 5) {
            $limit = 5;
        } elseif ($limit > 500) {
            $limit = 500;
        }

        if ($this->repo->saveLogViewLimit($user, $limit)) {
            Session::merge('userInfo', ['logs_per_page' => $limit]);
            return true;
        }
        return false;
    }

    public function saveTheme($theme = '', $user)
    {
        if (Session::get('userInfo')['theme'] == $theme) {
            return true;
        }

        if ($this->repo->saveUserTheme($user, $theme)) {
            Session::merge('userInfo', ['theme' => $theme]);
            Utils\View::setThemeCookie($theme);
            return true;
        }
        return false;
    }

    public function getSetting($setting, $userID)
    {
        return $this->repo->getUserSetting($userID, $setting);
    }
}
