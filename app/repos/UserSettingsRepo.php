<?php
/**
 * Repo for user settings
 */
namespace Dandelion\Repos;

use \Dandelion\Repos\Interfaces;

class UserSettingsRepo extends BaseRepo implements Interfaces\UserSettingsRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'user';
    }

    public function saveLogViewLimit($uid, $limit)
    {
        return $this->database->updateItem($this->table, $uid, ['logs_per_page' => $limit]);
    }

    public function saveUserTheme($uid, $theme)
    {
        return $this->database->updateItem($this->table, $uid, ['theme' => $theme]);
    }

    public function getUserSetting($uid, $setting)
    {
        return $this->database->find($this->table)->whereEqual('id', $uid)->readField($setting);
    }
}
