<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
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
