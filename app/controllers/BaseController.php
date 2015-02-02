<?php
/**
 * Base controller
 */
namespace Dandelion\Controllers;

use \Dandelion\Application;

class BaseController
{
	// Instance of running application
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}
}
