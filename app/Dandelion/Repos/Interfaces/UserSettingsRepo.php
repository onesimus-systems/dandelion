<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Repos\Interfaces;

interface UserSettingsRepo
{
    public function saveLogViewLimit($uid, $limit);
    public function saveUserTheme($uid, $theme);
    public function getUserSetting($uid, $setting);
}
