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
use Dandelion\Application;
use Dandelion\Utils\Repos;

class BaseController
{
    // Instance of running application
    protected $app;
    protected $rights;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function loadRights()
    {
        $rightsRepo = Repos::makeRepo('Groups');
        $this->rights = new Rights($_SESSION['userInfo']['id'], $rightsRepo);
    }

    protected function setResponse($body)
    {
        $this->app->response->setBody($body);
    }
}
