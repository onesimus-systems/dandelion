<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\v1;

use Dandelion\UserSettings;
use Dandelion\Utils\View;
use Dandelion\Exception\ApiException;
use Dandelion\API\ApiCommander;
use Dandelion\API\BaseModule;

class UserSettingsAPI extends BaseModule
{
    public function saveLogLimit($params)
    {
        return $this->saveSetting('LogLimit', $params->limit);
    }

    public function saveTheme($params)
    {
        return $this->saveSetting('Theme', $params->theme);
    }

    private function saveSetting($setting, $value)
    {
        $settings = new UserSettings($this->repo);
        $method = "save{$setting}";

        try {
            $settings->$method($value, $this->requestUser->get('id'));
        } catch(\PDOException $e) {
            throw new ApiException(
                'Error saving setting',
                ApiCommander::API_GENERAL_ERROR,
                0,
                $e->getMessage()
            );
        }

        return 'Setting saved';
    }

    public function getThemeList()
    {
        return View::getThemeListArray();
    }
}
