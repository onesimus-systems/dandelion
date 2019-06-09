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

use Dandelion\Rights;
use Dandelion\Utils\Repos;
use Dandelion\Application;
use Dandelion\Auth\GateKeeper;
use Dandelion\Utils\Configuration as Config;
use Dandelion\API\ApiCommander;
use Dandelion\API\Module\BaseModule;
use Dandelion\Exception\ApiException;
use Dandelion\Session\SessionManager as Session;
use Dandelion\User;
use Dandelion\Factories\UserFactory;

abstract class ApiController extends BaseController
{
    protected $moduleInitPath = '';
    protected $apiCommander;
    protected $startTime;

    /**
     * Inializer called by parent constructor
     * @return void
     */
    protected function init()
    {
        $this->app->response->headers->replace([
            'Content-Type'=> 'application/json',
            'Access-Control-Allow-Origin'=> '*',
        ]);

        $apiCommander = new ApiCommander();
        if ($this->moduleInitPath) {
            include $this->app->paths['app'].$this->moduleInitPath;
        }
        $this->apiCommander = $apiCommander;
    }

    /**
     * Process api call
     *
     * @param $module string - Name of api module to create
     * @param $method string - Method to call on module
     *
     * @return null
     */
    public function apiCall($module, $method) { }

    /**
     * Process internal api call
     *
     * @param $module string - Name of api module to create
     * @param $method string - Method to call on module
     *
     * @return null
     */
    public function internalApiCall($module, $method)
    {
        $this->startTime = microtime(true);
        if (!$this->isGoodApiCall($module, $method)) {
            return;
        }

        if (GateKeeper::authenticated()) {
            $this->setResponse($this->processRequest($this->sessionUser, $module, $method));
        } else {
            $this->setResponse($this->formatResponse(ApiCommander::API_LOGIN_REQUIRED, 'Action requires logged in session', 'api'));
        }
        return;
    }

    /**
     * Checks that the request is valid
     *
     * @param $module string - Name of api module to create
     * @param $method string - Method to call on module
     *
     * @return boolean
     */
    protected function isGoodApiCall($module, $method)
    {
        if (!$module || !$method) {
            $this->app->logger->notice(
                "Bad API call for Module: '{mod}' and Method: '{met}'",
                ['mod' => $module, 'met' => $method]
            );
            $this->setResponse($this->formatResponse(ApiCommander::API_INVALID_CALL, 'Bad API call', 'api'));
            return false;
        }
        return true;
    }

    /**
     * Process API request
     *
     * @param string $key - API key or userID
     * @param bool $localCall - Is the call from a Dandelion component
     * @param string $subsystem - Module being called
     * @param string $request - Method being called
     *
     * @return string json
     */
    protected function processRequest(?User $user, $module, $request)
    {
        try {
            $data = $this->apiCommander->dispatchModule(
                $module,
                $request,
                $this->app->request,
                [$this->app, $user]);

            return $this->formatResponse(ApiCommander::API_SUCCESS, 'Completed', $module, $data);
        } catch (ApiException $e) {
            if ($e->getCode() !== 1) { // Don't log invalid key exceptions
                $this->app->logger->error(
                    "{mess} :: Module: '{mod}'",
                    ['mess' => $e->getInternalMessage(), 'mod' => $e->getModule()]
                );
            }

            $this->setHttpCode($e->getHttpCode());
            return $this->formatResponse($e->getCode(), $e->getMessage(), $e->getModule(), '');
        } catch (\Exception $e) {
            $this->app->logger->error($e->getMessage());
            $this->setHttpCode(500);
            return $this->formatResponse(ApiCommander::API_SERVER_ERROR, 'Internal Server Error', 'api');
        }
    }

    /**
     * Checks database to see if API is present and therefore valid
     *
     * @param string $key - API key to verify
     *
     * @return bool
     */
    protected function verifyKey($key)
    {
        if (!$key) {
            return false;
        }

        $repo = Repos::makeRepo('Api');
        $keyValid = $repo->getKey($key);

        if ($keyValid) {
            return $keyValid['user_id'];
        }
        return false;
    }

    /**
     * Generate and return a JSON encoded response
     *
     * @param int $ecode - Error code
     * @param string $status - Text status message
     * @param string $module - API where DAPI was created
     * @param array $data - Data returned from API
     *
     * @return string json
     */
    protected function formatResponse($ecode, $status, $module, $data = '')
    {
        /**
         * Array composition:
         *
         * errorcode - Integer code corresponding to some error
         * status - String message of error or feedback
         * module - String name of the API module that was called
         * data - Data returned by API module
         * requestTime - Time it took for the request to finish
         *
         * Error Code Meanings are defined in the ApiCommander class.
         */
        $endTime = round(((microtime(true) - $this->startTime) * 1000), 2);
        $response = [
            'errorcode' => $ecode,
            'status' => $status,
            'module' => $module,
            'data' => $data ?: $status,
            'requestTime' => $endTime.'ms'
        ];
        return json_encode($response);
    }
}
