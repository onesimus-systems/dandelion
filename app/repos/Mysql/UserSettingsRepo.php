<?php
/**
 * MySQL repo for user settings
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class UserSettingsRepo extends BaseMySqlRepo implements Interfaces\UserSettingsRepo
{
    public function saveLogViewLimit($uid, $limit)
    {
        $this->database->update($this->prefix.'users')
                       ->set('showlimit = :limit')
                       ->where('userid = :uid');

        return $this->database->go(['limit' => $limit, 'uid' => $uid]);
    }

    public function saveUserTheme($uid, $theme)
    {
        $this->database->update($this->prefix.'users')
                       ->set('theme = :theme')
                       ->where('userid = :uid');

        return $this->database->go(['theme' => $theme, 'uid' => $uid]);
    }

    public function getUserSetting($uid, $setting)
    {
        $this->database->select($setting)
                       ->from($this->prefix.'users')
                       ->where('userid = :id');

        return $this->database->getFirst(['id' => $uid])[$setting];
    }
}
