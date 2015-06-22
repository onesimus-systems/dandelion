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

class SettingsController extends BaseController
{
    public function settings()
    {
        $this->loadRights();

        $template = new Template($this->app);

        $key = '';
        if ($this->app->config['publicApiEnabled']) {
            $keyManager = new KeyManager(Repos::makeRepo('KeyManager'));
            $key = $keyManager->getKey($_SESSION['userInfo']['id']);
        }

        $template->addData([
            'publicApiEnabled' => $this->app->config['publicApiEnabled'],
            'apiKey' => $key,
            'themeInfo' => View::getThemeListArray(),
            'logsPerPage' => $_SESSION['userInfo']['logs_per_page']
        ]);

        $template->render('settings', 'User Settings');
    }
}
