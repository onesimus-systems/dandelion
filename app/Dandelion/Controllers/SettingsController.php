<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Controllers;

use Dandelion\Template;
use Dandelion\Utils\View;
use Dandelion\Utils\Repos;
use Dandelion\KeyManager;
use Dandelion\Utils\Configuration as Config;
use Dandelion\Session\SessionManager as Session;

class SettingsController extends BaseController
{
    public function settings()
    {
        $template = new Template($this->app);

        $apiEnabled = Config::get('publicApiEnabled') && $this->sessionUser->get('api_override') > 0;

        $key = '';
        if ($apiEnabled) {
            $keyManager = new KeyManager(Repos::makeRepo('KeyManager'));
            $key = $keyManager->getKey($this->sessionUser->get('id'));
        }

        $template->addData([
            'publicApiEnabled' => $apiEnabled,
            'apiKey' => $key,
            'themeInfo' => View::getThemeListArray(),
            'logsPerPage' => $this->sessionUser->get('logs_per_page')
        ]);

        $this->setResponse($template->render('settings', 'User Settings'));
    }
}
