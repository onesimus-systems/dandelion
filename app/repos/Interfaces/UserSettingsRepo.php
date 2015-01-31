<?php
/**
 * Repo interface for user settings
 */
namespace Dandelion\Repos\Interfaces;

interface UserSettingsRepo
{
    public function saveLogViewLimit($uid, $limit);
    public function saveUserTheme($uid, $theme);
    public function getUserSetting($uid, $setting);
}
