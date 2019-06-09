<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API;

use Dandelion\User;
use Dandelion\Application;
use Dandelion\Auth\GateKeeper;
use Dandelion\Utils\Repos;
use Dandelion\API\ApiCommander;
use Dandelion\Exception\ApiException;
use Dandelion\Controllers\ApiController;

/**
 * Shim class until conversion to Keycard authorization
 * TODO: Remove shim class
 */
class UserRightsShim
{
    private $card;
    public $userid;

    public function __construct(User $user)
    {
        $this->card = $user->getKeycard();
        $this->userid = $user->get('id');
    }

    public function authorized($permission)
    {
        return $this->card->read($permission);
    }

    public function isAdmin()
    {
        return $this->authorized('admin');
    }
}

abstract class BaseModule
{
    // Shim classes
    // TODO: Remove properties
    protected $ur;

    // Application
    protected $app;

    // User rights
    protected $requestUser;

    // URL parameters
    protected $request;

    // Repo for the specific module
    protected $repo;

    protected $makeRepo = true;

    public function __construct(Application $app, ?User $user) {
        $this->app = $app;
        $this->requestUser = $user;
        $this->request = $app->request;
        // TODO: Remove these objects
        if ($user) {
            $this->ur = new UserRightsShim($user);
        }

        if ($this->makeRepo) {
            // Remove namespace
            $module = array_reverse(explode('\\', get_class($this)));
            // Remove the API at the end of the class name
            $module = substr($module[0], 0, -3);
            $this->repo = $this->makeRepo($module);
        }
    }

    protected function makeRepo($module)
    {
        $repo = Repos::makeRepo($module);
        if ($repo) {
            return $repo;
        } else {
            throw new ApiException('Error initializing API request', ApiCommander::API_SERVER_ERROR);
        }
    }

    protected function authorized(User $user, $task)
    {
        return GateKeeper::authorized($user, $task);
    }
}
