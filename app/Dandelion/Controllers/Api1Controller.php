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

use Dandelion\Utils\Configuration as Config;
use Dandelion\API\ApiCommander;
use Dandelion\Exception\ApiException;
use Dandelion\Factories\UserFactory;

class Api1Controller extends ApiController
{
    protected $moduleInitPath = '/Dandelion/API/v1/init.php';

    /**
     * Process api call
     *
     * @param $module string - Name of api module to create
     * @param $method string - Method to call on module
     *
     * @return null
     */
    public function apiCall($module, $method)
    {
        $publicEnabled = Config::get('publicApiEnabled');
        // If the full public api is enabled, whitelisting is not
        $whitelistEnabled = $publicEnabled ? false : Config::get('whitelistApiEnabled');

        $this->startTime = microtime(true);
        if (!$this->isGoodApiCall($module, $method)) {
            return;
        }

        if (!$publicEnabled && !$whitelistEnabled) {
            $this->setResponse($this->formatResponse(ApiCommander::API_DISABLED, 'Public API disabled', 'api'));
            return;
        }

        $apikey = $this->request->isGet() ?
                  $this->request->getParam('apikey', '') :
                  $this->request->postParam('apikey', '');

        $userid = $this->verifyKey($apikey);

        if ($userid === false) {
            $msg = $publicEnabled ? 'Invalid API key' : 'Public API disabled';
            $this->setResponse($this->formatResponse(ApiCommander::API_DISABLED, $msg, 'api'));
            return;
        }

        // API key is valid
        $user = (new UserFactory())->getWithKeycard($userid);

        if (!$user->enabled() || !$user->get('api_override')) {
            $this->setResponse($this->formatResponse(ApiCommander::API_INSUFFICIENT_PERMISSIONS, 'API disabled', 'api'));
            return;
        }

        if ($whitelistEnabled && $user->get('api_override') != 1) {
            $this->setResponse($this->formatResponse(ApiCommander::API_INSUFFICIENT_PERMISSIONS, 'API disabled', 'api'));
            return;
        }

        $this->setResponse($this->processRequest($user, $module, $method));
        return;
    }
}
