<?php
/**
 * Base controller
 */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Application;
use \Dandelion\Utils\Repos;

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
        $rightsRepo = Repos::makeRepo('Rights');
        $this->rights = new Rights($_SESSION['userInfo']['userid'], $rightsRepo);
    }
}
