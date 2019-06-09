<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\v2;

use Dandelion\Auth\GateKeeper;
use Dandelion\API\ApiCommander;
use Dandelion\API\BaseModule;
use Dandelion\Exception\ApiException;
use Dandelion\Factories\UserFactory;
use Dandelion\Utils\Configuration as Config;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class AuthAPI extends BaseModule
{
    protected $makeRepo = false;

    public function login($params)
    {
        $user = (new UserFactory())->getByUsername($params->username);
        if (!$user->isValid()) {
            throw new ApiException('Invalid username or password', ApiCommander::API_INVALID_KEY);
        }

        if (!$user->enabled() || !$user->get('api_override')) {
            $this->setResponse($this->formatResponse(ApiCommander::API_INSUFFICIENT_PERMISSIONS, 'API disabled', 'api'));
            return;
        }

        $whitelistEnabled = Config::get('publicApiEnabled') ? false : Config::get('whitelistApiEnabled');
        if ($whitelistEnabled && $user->get('api_override') != 1) {
            $this->setResponse($this->formatResponse(ApiCommander::API_INSUFFICIENT_PERMISSIONS, 'API disabled', 'api'));
            return;
        }

        $auth = new GateKeeper();
        if (!$auth->checkPassword($user, $params->password)) {
            throw new ApiException('Invalid username or password', ApiCommander::API_INVALID_KEY);
        }

        $signer = new Sha256();
        $time = time();

        $token = (new Builder())->issuedBy('Dandelion')
                        ->permittedFor('Dandelion')
                        ->issuedAt($time)
                        ->canOnlyBeUsedAfter($time)
                        ->expiresAt($time + 3600)
                        ->withClaim('userid', $user->id)
                        ->getToken($signer, new Key(Config::get('jwtSecret')));

        return [
            'token' => (string)$token,
        ];
    }
}
