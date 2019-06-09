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

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class Api2Controller extends ApiController
{
    protected $moduleInitPath = '/Dandelion/API/v2/init.php';

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

        if ($module == 'auth' && $method == 'login') {
            $this->setResponse($this->processRequest(null, $module, $method));
            return;
        }

        $user = $this->checkAuthToken();

        if (!$user) {
            $this->setResponse($this->formatResponse(ApiCommander::API_DISABLED, 'Invalid auth token', 'api'));
            return;
        }

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

    private function checkAuthToken()
    {
        $authHeader = $this->request->headers->get('Authorization');
        if (!$authHeader) {
            return;
        }

        $authHeaderParts = explode(' ', $authHeader, 2);
        if (count($authHeaderParts) != 2 || $authHeaderParts[0] !== 'Bearer') {
            return;
        }

        try {
            $token = (new Parser())->parse((string) $authHeaderParts[1]);
        } catch (\Exception $e) {
            $this->app->logger->error($e);
            return;
        }

        $signer = new Sha256();
        if (!$token->verify($signer, Config::get('jwtSecret'))) {
            return;
        }

        $data = new ValidationData();
        $data->setIssuer('Dandelion');
        $data->setAudience('Dandelion');

        if (!$token->validate($data)) {
            return;
        }

        return (new UserFactory())->getWithKeycard($token->getClaim('userid'));
    }
}
