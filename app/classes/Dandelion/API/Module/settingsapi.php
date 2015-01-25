<?php
/**
 * User settings management API module
 */
namespace Dandelion\API\Module;

use \Dandelion\UserSettings;
use \Dandelion\Controllers\ApiController;

class settingsAPI extends BaseModule
{
    public function __construct($db, $ur, $params)
    {
        parent::__construct($db, $ur, $params);
    }

    public function saveLogLimit()
    {
        $settings = new UserSettings($this->db);
        return json_encode($settings->saveLogLimit($this->up->limit));
    }

    public function saveTheme()
    {
        $settings = new UserSettings($this->db);
        return json_encode($settings->saveTheme($this->up->theme));
    }

    public function getThemeList()
    {
        return json_encode(\Dandelion\getThemeListArray());
    }
}
